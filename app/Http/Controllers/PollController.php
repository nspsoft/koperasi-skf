<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PollController extends Controller
{
    public function index()
    {
        $polls = Poll::withCount('votes')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('polls.index', compact('polls'));
    }

    public function create()
    {
        return view('polls.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'candidates' => 'required|array|min:2',
            'candidates.*.name' => 'required|string|max:255',
            'candidates.*.photo' => 'nullable|image|max:2048',
            'candidates.*.vision_mission' => 'nullable|string',
        ]);

        $poll = Poll::create([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'draft',
        ]);

        foreach ($request->candidates as $candidate) {
            $photoPath = null;
            if (isset($candidate['photo'])) {
                $photoPath = $candidate['photo']->store('candidates', 'public');
            }

            $poll->options()->create([
                'candidate_name' => $candidate['name'],
                'candidate_photo' => $photoPath,
                'vision_mission' => $candidate['vision_mission'],
            ]);
        }

        return redirect()->route('polls.index')->with('success', 'Pemilihan berhasil dibuat dalam draf.');
    }

    public function show(Poll $poll)
    {
        $poll->load('options');
        $userVote = $poll->userVote(auth()->id());

        return view('polls.show', compact('poll', 'userVote'));
    }

    public function vote(Request $request, Poll $poll)
    {
        $request->validate([
            'poll_option_id' => 'required|exists:poll_options,id',
        ]);

        // Check if poll is active
        if ($poll->status !== 'active') {
            return back()->with('error', 'Pemilihan ini tidak sedang aktif.');
        }

        // Check if within time
        $now = Carbon::now();
        if ($now->lt($poll->start_date) || $now->gt($poll->end_date)) {
            return back()->with('error', 'Pemilihan berada di luar periode waktu yang ditentukan.');
        }

        // Check if user already voted
        if ($poll->userVote(auth()->id())) {
            return back()->with('error', 'Anda sudah memberikan suara dalam pemilihan ini.');
        }

        PollVote::create([
            'poll_id' => $poll->id,
            'user_id' => auth()->id(),
            'poll_option_id' => $request->poll_option_id,
        ]);

        return back()->with('success', 'Terima kasih! Suara Anda telah berhasil dikirim.');
    }

    public function results(Poll $poll)
    {
        $poll->load(['options' => function($query) {
            $query->withCount('votes');
        }]);

        $totalVotes = $poll->votes()->count();

        return view('polls.results', compact('poll', 'totalVotes'));
    }

    public function updateStatus(Request $request, Poll $poll)
    {
        $request->validate([
            'status' => 'required|in:draft,active,closed',
        ]);

        $oldStatus = $poll->status;
        $poll->update(['status' => $request->status]);

        // Send WhatsApp notifications when poll is activated
        if ($oldStatus !== 'active' && $request->status === 'active') {
            \App\Jobs\SendPollNotification::dispatch($poll);
        }

        return back()->with('success', 'Status pemilihan berhasil diperbarui.');
    }

    public function destroy(Poll $poll)
    {
        $poll->delete();
        return redirect()->route('polls.index')->with('success', 'Pemilihan berhasil dihapus.');
    }
}
