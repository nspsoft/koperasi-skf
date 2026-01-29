@extends('layouts.app')

@section('title', $poll->title)

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $poll->title }}</h1>
            <p class="page-subtitle">{{ $poll->status === 'active' ? 'Silakan berikan suara Anda' : 'Detail Pemilihan' }}</p>
        </div>
        <a href="{{ route('polls.index') }}" class="btn-secondary">Kembali</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Info Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <div class="glass-card-solid p-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Informasi Pemilihan</h3>
                <div class="space-y-4">
                    <div>
                        <label class="text-xs text-gray-500 block mb-1">Status</label>
                        <span class="text-xs font-bold px-3 py-1 rounded-full uppercase {{ $poll->status === 'active' ? 'bg-green-500 text-white' : ($poll->status === 'closed' ? 'bg-red-500 text-white' : 'bg-gray-500 text-white') }}">
                            {{ $poll->status }}
                        </span>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 block mb-1">Periode</label>
                        <p class="text-xs text-gray-700 dark:text-gray-300">
                            {{ $poll->start_date->format('d M Y, H:i') }} <br>
                            s/d <br>
                            {{ $poll->end_date->format('d M Y, H:i') }}
                        </p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 block mb-1">Deskripsi</label>
                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed italic">
                            {{ $poll->description ?? 'Tidak ada deskripsi.' }}
                        </p>
                    </div>
                </div>
            </div>

            @if($userVote)
                <div class="p-4 bg-primary-50 dark:bg-primary-900/20 rounded-xl border border-primary-200 dark:border-primary-800">
                    <div class="flex items-center text-primary-700 dark:text-primary-300 mb-2">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-bold text-sm">Anda Sudah Memilih</span>
                    </div>
                    <p class="text-xs text-primary-600 dark:text-primary-400">
                        Suara Anda telah berhasil dikirim pada {{ $userVote->created_at->format('d M Y, H:i') }}.
                    </p>
                </div>
            @endif
        </div>

        <!-- Candidates List -->
        <div class="lg:col-span-3">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($poll->options as $option)
                    <div class="glass-card-solid overflow-hidden flex flex-col hover:border-primary-500/30 transition-all {{ $userVote && $userVote->poll_option_id === $option->id ? 'border-primary-500 ring-1 ring-primary-500' : '' }}">
                        <div class="aspect-square bg-gray-100 dark:bg-gray-800 relative">
                            @if($option->candidate_photo)
                                <img src="{{ Storage::url($option->candidate_photo) }}" alt="{{ $option->candidate_name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                    <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            @endif
                            
                            @if($userVote && $userVote->poll_option_id === $option->id)
                                <div class="absolute top-0 right-0 m-4 bg-primary-500 text-white rounded-full p-2 shadow-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <div class="p-6 flex-grow flex flex-col">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $option->candidate_name }}</h3>
                            <div class="mb-6 flex-grow">
                                <label class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1 block">Visi & Misi</label>
                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-4 italic">
                                    {{ $option->vision_mission ?? 'Tidak ada informasi visi & misi.' }}
                                </p>
                            </div>

                            @if($poll->status === 'active' && !$userVote)
                                <form action="{{ route('polls.vote', $poll->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memberikan suara kepada {{ $option->candidate_name }}?')">
                                    @csrf
                                    <input type="hidden" name="poll_option_id" value="{{ $option->id }}">
                                    <button type="submit" class="btn-primary w-full justify-center py-3 shadow-lg shadow-primary-500/20">
                                        Pilih Kandidat
                                    </button>
                                </form>
                            @elseif($userVote && $userVote->poll_option_id === $option->id)
                                <button disabled class="w-full py-3 rounded-xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 font-bold text-sm border border-primary-500/30">
                                    Pilihan Anda
                                </button>
                            @else
                                <button disabled class="w-full py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-400 font-bold text-sm">
                                    {{ $poll->status === 'closed' ? 'Pemilihan Berakhir' : ($poll->status === 'draft' ? 'Belum Dimulai' : 'Sudah Memilih') }}
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
