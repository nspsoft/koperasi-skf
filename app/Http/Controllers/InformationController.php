<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShuSetting;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class InformationController extends Controller
{
    public function adArt()
    {
        // Get latest SHU settings for percentage display
        $shuSetting = ShuSetting::orderBy('period_year', 'desc')->first();
        
        // Get cooperative settings as key-value array
        $settings = Setting::pluck('value', 'key')->toArray();
        
        // Add robust defaults for AD/ART versioning if missing from DB
        $settings['ad_art_version'] = $settings['ad_art_version'] ?? '3.0';
        $settings['ad_art_ratification_date'] = $settings['ad_art_ratification_date'] ?? '15 Januari 2026';
        
        return view('information.ad-art', compact('shuSetting', 'settings'));
    }

    public function downloadAdArtPdf()
    {
        // Get latest SHU settings for percentage display
        $shuSetting = ShuSetting::orderBy('period_year', 'desc')->first();
        
        // Get cooperative settings as key-value array
        $settings = Setting::pluck('value', 'key')->toArray();
        
        // Prepare QR Code for verification
        $verificationUrl = route('ad-art');
        $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($verificationUrl);
        
        try {
            $qrImageData = @file_get_contents($qrApiUrl);
            $qrCode = $qrImageData ? 'data:image/png;base64,' . base64_encode($qrImageData) : null;
        } catch (\Exception $e) {
            $qrCode = null;
        }

        // Prepare logos for official header using settings
        $logo1Path = isset($settings['doc_logo_1']) ? storage_path('app/public/' . $settings['doc_logo_1']) : null;
        $logo2Path = isset($settings['doc_logo_2']) ? storage_path('app/public/' . $settings['doc_logo_2']) : null;

        $logo1 = ($logo1Path && file_exists($logo1Path)) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logo1Path)) : null;
        $logo2 = ($logo2Path && file_exists($logo2Path)) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logo2Path)) : null;

        // Fallback to public images if settings logos are missing
        if (!$logo1) {
            $fallbackLogo1 = public_path('images/spindo-logo.png');
            $logo1 = file_exists($fallbackLogo1) ? 'data:image/png;base64,' . base64_encode(file_get_contents($fallbackLogo1)) : null;
        }

        // Get coop name for filename
        $coopName = $settings['coop_name'] ?? 'Koperasi';
        $cleanName = preg_replace('/[^A-Za-z0-9]/', '_', $coopName);
        
        $pdf = Pdf::loadView('information.ad-art-pdf', compact('shuSetting', 'settings', 'qrCode', 'logo1', 'logo2'))
            ->setPaper('a4', 'portrait');
        
        if (request('print')) {
            return $pdf->stream("AD-ART_{$cleanName}.pdf");
        }
        
        return $pdf->download("AD-ART_{$cleanName}.pdf");
    }

    public function governance()
    {
        // Get cooperative settings as key-value array
        $settings = Setting::pluck('value', 'key')->toArray();
        
        return view('information.governance', compact('settings'));
    }

    public function downloadGovernancePdf()
    {
        // Get cooperative settings as key-value array
        $settings = Setting::pluck('value', 'key')->toArray();
        
        // Prepare QR Code for verification
        $verificationUrl = route('governance');
        $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($verificationUrl);
        
        try {
            $qrImageData = @file_get_contents($qrApiUrl);
            $qrCode = $qrImageData ? 'data:image/png;base64,' . base64_encode($qrImageData) : null;
        } catch (\Exception $e) {
            $qrCode = null;
        }

        // Prepare logos for official header using settings
        $logo1Path = isset($settings['doc_logo_1']) ? storage_path('app/public/' . $settings['doc_logo_1']) : null;
        $logo2Path = isset($settings['doc_logo_2']) ? storage_path('app/public/' . $settings['doc_logo_2']) : null;

        $logo1 = ($logo1Path && file_exists($logo1Path)) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logo1Path)) : null;
        $logo2 = ($logo2Path && file_exists($logo2Path)) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logo2Path)) : null;

        // Fallback to public images if settings logos are missing
        if (!$logo1) {
            $fallbackLogo1 = public_path('images/spindo-logo.png');
            $logo1 = file_exists($fallbackLogo1) ? 'data:image/png;base64,' . base64_encode(file_get_contents($fallbackLogo1)) : null;
        }

        // Get coop name for filename
        $coopName = $settings['coop_name'] ?? 'Koperasi';
        $cleanName = preg_replace('/[^A-Za-z0-9]/', '_', $coopName);
        
        $pdf = Pdf::loadView('information.governance-pdf', compact('settings', 'qrCode', 'logo1', 'logo2'))
            ->setPaper('a4', 'portrait');
        
        if (request('print')) {
            return $pdf->stream("Tugas_Wewenang_{$cleanName}.pdf");
        }
        
        return $pdf->download("Tugas_Wewenang_{$cleanName}.pdf");
    }

    public function documentation()
    {
        return view('information.documentation');
    }

    public function establishment()
    {
        return view('information.establishment');
    }

    public function installEstablishment()
    {
        $seeder = new \Database\Seeders\EstablishmentDocumentSeeder();
        $seeder->run();
        
        return redirect()->route('establishment')->with('success', 'Template Dokumen Pendirian berhasil diinstall! Silakan cek menu Buat Dokumen.');
    }

    public function uat()
    {
        return view('information.uat');
    }
}

