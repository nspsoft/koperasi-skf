<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LandingSettingController extends Controller
{
    /**
     * Show the landing page settings form.
     */
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $teamMembers = \App\Models\TeamMember::orderBy('order')->get();
        $workPrograms = \App\Models\WorkProgram::orderBy('order')->get();
        return view('settings.landing', compact('settings', 'teamMembers', 'workPrograms'));
    }

    /**
     * Store new Team Member
     */
    public function storeMember(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'bio' => 'nullable|string',
        ]);

        try {
            $path = $request->file('image')->store('team', 'public');
            
            \App\Models\TeamMember::create([
                'name' => $request->name,
                'role' => $request->role,
                'image' => $path,
                'bio' => $request->bio,
                'twitter_link' => $request->twitter_link,
                'facebook_link' => $request->facebook_link,
                'instagram_link' => $request->instagram_link,
                'linkedin_link' => $request->linkedin_link,
            ]);

            return redirect()->back()->with('success', 'Anggota Tim berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan anggota: ' . $e->getMessage());
        }
    }

    /**
     * Delete Team Member
     */
    public function destroyMember($id)
    {
        try {
            $member = \App\Models\TeamMember::findOrFail($id);
            if ($member->image) {
                Storage::disk('public')->delete($member->image);
            }
            $member->delete();
            return redirect()->back()->with('success', 'Anggota Tim berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus anggota: ' . $e->getMessage());
        }
    }

    /**
     * Update the landing page settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'landing_hero_title' => 'nullable|string|max:255',
            'landing_hero_subtitle' => 'nullable|string|max:500',
            'landing_about_text' => 'nullable|string|max:1000',
            'landing_visi' => 'nullable|string|max:1000',
            'landing_misi' => 'nullable|string|max:2000',
            'landing_program_kerja' => 'nullable|string|max:3000',
            'hero_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'struktur_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        try {
            DB::beginTransaction();

            // Handle Hero Image Upload
            if ($request->hasFile('hero_image')) {
                $oldImage = Setting::where('key', 'landing_hero_image')->first();
                if ($oldImage && $oldImage->value) {
                    Storage::disk('public')->delete($oldImage->value);
                }

                $path = $request->file('hero_image')->store('settings', 'public');
                Setting::updateOrCreate(
                    ['key' => 'landing_hero_image'],
                    ['value' => $path, 'group' => 'landing']
                );
            }

            // Handle Struktur Organisasi Image Upload
            if ($request->hasFile('struktur_image')) {
                $oldImage = Setting::where('key', 'landing_struktur_image')->first();
                if ($oldImage && $oldImage->value) {
                    Storage::disk('public')->delete($oldImage->value);
                }

                $path = $request->file('struktur_image')->store('settings', 'public');
                Setting::updateOrCreate(
                    ['key' => 'landing_struktur_image'],
                    ['value' => $path, 'group' => 'landing']
                );
            }

            // Handle Feature Images Upload
            for ($i = 1; $i <= 3; $i++) {
                $fieldName = "feature{$i}_image";
                if ($request->hasFile($fieldName)) {
                    $oldImage = Setting::where('key', "landing_feature{$i}_image")->first();
                    if ($oldImage && $oldImage->value) {
                        Storage::disk('public')->delete($oldImage->value);
                    }

                    $path = $request->file($fieldName)->store('settings', 'public');
                    Setting::updateOrCreate(
                        ['key' => "landing_feature{$i}_image"],
                        ['value' => $path, 'group' => 'landing']
                    );
                }
            }

            // Handle About Section Image Upload
            if ($request->hasFile('about_image')) {
                $oldImage = Setting::where('key', 'landing_about_image')->first();
                if ($oldImage && $oldImage->value) {
                    Storage::disk('public')->delete($oldImage->value);
                }

                $path = $request->file('about_image')->store('settings', 'public');
                Setting::updateOrCreate(
                    ['key' => 'landing_about_image'],
                    ['value' => $path, 'group' => 'landing']
                );
            }

            // Update text settings
            $textFields = [
                // Hero Section
                'landing_hero_title', 
                'landing_hero_subtitle', 
                // Features Section
                'landing_feature1_title',
                'landing_feature1_desc',
                'landing_feature2_title',
                'landing_feature2_desc',
                'landing_feature3_title',
                'landing_feature3_desc',
                // About Section
                'landing_about_title',
                'landing_about_text',
                'landing_about_highlight1_title',
                'landing_about_highlight1_desc',
                'landing_about_highlight2_title',
                'landing_about_highlight2_desc',
                // Visi Misi
                'landing_visi',
                'landing_misi',
                // Program Kerja
                'landing_program_kerja',
                // CTA Section
                'landing_cta_title',
                'landing_cta_subtitle',
                // Footer
                'landing_footer_desc',
                'landing_social_twitter',
                'landing_social_facebook',
                'landing_social_instagram',
            ];
            foreach ($textFields as $field) {
                if ($request->has($field)) {
                    Setting::updateOrCreate(
                        ['key' => $field],
                        ['value' => $request->input($field), 'group' => 'landing']
                    );
                }
            }

            DB::commit();

            return redirect()->route('settings.landing')->with('success', 'Konfigurasi Landing Page berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Store new Work Program
     */
    public function storeProgram(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:1024',
            'color' => 'required|string|in:green,blue,purple,orange,teal,pink',
        ]);

        try {
            $data = [
                'title' => $request->title,
                'description' => $request->description,
                'color' => $request->color,
                'order' => \App\Models\WorkProgram::max('order') + 1,
            ];

            if ($request->hasFile('icon')) {
                $data['icon'] = $request->file('icon')->store('programs', 'public');
            }
            
            \App\Models\WorkProgram::create($data);

            return redirect()->back()->with('success', 'Program Kerja berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan program: ' . $e->getMessage());
        }
    }

    /**
     * Delete Work Program
     */
    public function destroyProgram($id)
    {
        try {
            $program = \App\Models\WorkProgram::findOrFail($id);
            
            if ($program->icon) {
                Storage::disk('public')->delete($program->icon);
            }
            
            $program->delete();
            
            return redirect()->back()->with('success', 'Program Kerja berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus program: ' . $e->getMessage());
        }
    }
}
