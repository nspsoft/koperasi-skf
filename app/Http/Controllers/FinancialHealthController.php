<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\Member;
use App\Models\Saving;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialHealthController extends Controller
{
    public function index()
    {
        $metrics = $this->calculateMetrics();
        $trends = $this->calculateTrends();
        $alerts = $this->generateAlerts($metrics);
        
        return view('reports.financial-health', compact('metrics', 'trends', 'alerts'));
    }
    
    public function getMetrics()
    {
        return response()->json([
            'metrics' => $this->calculateMetrics(),
            'trends' => $this->calculateTrends(),
            'alerts' => $this->generateAlerts($this->calculateMetrics()),
        ]);
    }
    
    private function calculateMetrics(): array
    {
        // Total Simpanan (Kas Koperasi)
        $totalSimpanan = Saving::where('transaction_type', 'deposit')->sum('amount') 
                       - Saving::where('transaction_type', 'withdrawal')->sum('amount');
        
        // Total Pinjaman Aktif
        $totalPinjaman = Loan::where('status', 'active')->sum('amount');
        $sisaPinjaman = Loan::where('status', 'active')->sum('remaining_amount');
        
        // Pinjaman Bermasalah (NPL) - Overdue > 30 hari
        $pinjamanBermasalah = LoanPayment::where('status', 'pending')
            ->where('due_date', '<', Carbon::now()->subDays(30))
            ->sum('amount');
        
        // Kredit Macet - Overdue > 90 hari
        $kreditMacet = Transaction::where('payment_method', 'kredit')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->where('created_at', '<', Carbon::now()->subDays(90))
            ->sum('total_amount');
        
        $totalKredit = Transaction::where('payment_method', 'kredit')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->sum('total_amount');
        
        // Anggota
        $totalAnggota = Member::where('status', 'active')->count();
        $anggotaBulanIni = Member::where('status', 'active')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        $anggotaBulanLalu = Member::where('status', 'active')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();
        
        // Hitung Rasio
        $rasioNPL = $totalPinjaman > 0 ? ($pinjamanBermasalah / $totalPinjaman) * 100 : 0;
        $rasioKreditMacet = $totalKredit > 0 ? ($kreditMacet / $totalKredit) * 100 : 0;
        $rasioLikuiditas = $sisaPinjaman > 0 ? $totalSimpanan / $sisaPinjaman : 999;
        $pertumbuhanAnggota = $anggotaBulanLalu > 0 
            ? (($anggotaBulanIni - $anggotaBulanLalu) / $anggotaBulanLalu) * 100 
            : ($anggotaBulanIni > 0 ? 100 : 0);
        
        // Collection Rate
        $totalTagihan = LoanPayment::whereMonth('due_date', Carbon::now()->month)
            ->whereYear('due_date', Carbon::now()->year)
            ->sum('amount');
        $totalTerbayar = LoanPayment::where('status', 'paid')
            ->whereMonth('due_date', Carbon::now()->month)
            ->whereYear('due_date', Carbon::now()->year)
            ->sum('amount');
        $collectionRate = $totalTagihan > 0 ? ($totalTerbayar / $totalTagihan) * 100 : 100;
        
        return [
            'total_simpanan' => $totalSimpanan,
            'total_pinjaman' => $totalPinjaman,
            'sisa_pinjaman' => $sisaPinjaman,
            'pinjaman_bermasalah' => $pinjamanBermasalah,
            'kredit_macet' => $kreditMacet,
            'total_anggota' => $totalAnggota,
            'anggota_bulan_ini' => $anggotaBulanIni,
            
            // Rasio
            'rasio_npl' => round($rasioNPL, 2),
            'rasio_kredit_macet' => round($rasioKreditMacet, 2),
            'rasio_likuiditas' => round(min($rasioLikuiditas, 5), 2), // Cap at 5 for display
            'pertumbuhan_anggota' => round($pertumbuhanAnggota, 2),
            'collection_rate' => round($collectionRate, 2),
            
            // Status (green/yellow/red)
            'status_npl' => $rasioNPL < 5 ? 'green' : ($rasioNPL < 10 ? 'yellow' : 'red'),
            'status_kredit_macet' => $rasioKreditMacet < 3 ? 'green' : ($rasioKreditMacet < 7 ? 'yellow' : 'red'),
            'status_likuiditas' => $rasioLikuiditas > 1.5 ? 'green' : ($rasioLikuiditas > 1 ? 'yellow' : 'red'),
            'status_pertumbuhan' => $pertumbuhanAnggota > 0 ? 'green' : ($pertumbuhanAnggota == 0 ? 'yellow' : 'red'),
            'status_collection' => $collectionRate > 90 ? 'green' : ($collectionRate > 75 ? 'yellow' : 'red'),
        ];
    }
    
    private function calculateTrends(): array
    {
        $trends = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            
            // Simpanan per bulan
            $simpanan = Saving::whereMonth('transaction_date', $month->month)
                ->whereYear('transaction_date', $month->year)
                ->where('transaction_type', 'deposit')
                ->sum('amount');
            
            // Pinjaman dicairkan per bulan
            $pinjaman = Loan::whereMonth('disbursement_date', $month->month)
                ->whereYear('disbursement_date', $month->year)
                ->sum('amount');
            
            // Anggota baru per bulan
            $anggotaBaru = Member::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();
            
            $trends[] = [
                'bulan' => $month->translatedFormat('M Y'),
                'simpanan' => $simpanan,
                'pinjaman' => $pinjaman,
                'anggota_baru' => $anggotaBaru,
            ];
        }
        
        return $trends;
    }
    
    private function generateAlerts(array $metrics): array
    {
        $alerts = [];
        
        if ($metrics['status_npl'] === 'red') {
            $alerts[] = [
                'type' => 'danger',
                'title' => 'NPL Tinggi!',
                'message' => "Rasio NPL mencapai {$metrics['rasio_npl']}%. Segera lakukan penagihan intensif.",
                'icon' => '⚠️',
            ];
        } elseif ($metrics['status_npl'] === 'yellow') {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'NPL Perlu Perhatian',
                'message' => "Rasio NPL di {$metrics['rasio_npl']}%. Monitor ketat pembayaran anggota.",
                'icon' => '🔔',
            ];
        }
        
        if ($metrics['status_likuiditas'] === 'red') {
            $alerts[] = [
                'type' => 'danger',
                'title' => 'Likuiditas Rendah!',
                'message' => "Rasio likuiditas hanya {$metrics['rasio_likuiditas']}. Kurangi pencairan pinjaman baru.",
                'icon' => '🚨',
            ];
        }
        
        if ($metrics['status_collection'] === 'red') {
            $alerts[] = [
                'type' => 'danger',
                'title' => 'Collection Rate Rendah!',
                'message' => "Tingkat penagihan bulan ini hanya {$metrics['collection_rate']}%. Tingkatkan follow-up.",
                'icon' => '📉',
            ];
        }
        
        if ($metrics['status_pertumbuhan'] === 'red') {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Anggota Menurun',
                'message' => "Pertumbuhan anggota negatif ({$metrics['pertumbuhan_anggota']}%). Evaluasi program rekrutmen.",
                'icon' => '👥',
            ];
        }
        
        if (empty($alerts)) {
            $alerts[] = [
                'type' => 'success',
                'title' => 'Kondisi Sehat! ✅',
                'message' => 'Semua indikator keuangan dalam kondisi baik.',
                'icon' => '🎉',
            ];
        }
        
        return $alerts;
    }
}
