<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberRequest;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    /**
     * Display a listing of members.
     */
    public function index(Request $request)
    {
        if (! auth()->user()->hasAdminAccess()) {
            abort(403, 'Unauthorized action.');
        }
        $query = Member::with('user')
            ->when($request->search, function ($q) use ($request) {
                $q->where('member_id', 'like', '%'.$request->search.'%')
                    ->orWhere('employee_id', 'like', '%'.$request->search.'%')
                    ->orWhereHas('user', function ($userQuery) use ($request) {
                        $userQuery->where('name', 'like', '%'.$request->search.'%')
                            ->orWhere('email', 'like', '%'.$request->search.'%');
                    });
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->department, function ($q) use ($request) {
                $q->where('department', $request->department);
            });

        $members = $query->latest()->paginate(15)->withQueryString();

        // Get filter options
        $departments = Member::select('department')
            ->distinct()
            ->whereNotNull('department')
            ->pluck('department');

        return view('members.index', compact('members', 'departments'));
    }

    /**
     * Export members data to Excel.
     */
    public function export(Request $request)
    {
        if (! auth()->user()->hasAdminAccess()) {
            abort(403);
        }

        $query = Member::with('user')
            ->when($request->search, function ($q) use ($request) {
                $q->where('member_id', 'like', '%'.$request->search.'%')
                    ->orWhere('employee_id', 'like', '%'.$request->search.'%')
                    ->orWhereHas('user', function ($userQuery) use ($request) {
                        $userQuery->where('name', 'like', '%'.$request->search.'%')
                            ->orWhere('email', 'like', '%'.$request->search.'%');
                    });
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->department, function ($q) use ($request) {
                $q->where('department', $request->department);
            });

        $members = $query->get();

        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Anggota');

        // Style Settings
        $titleStyle = [
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ];
        $subtitleStyle = [
            'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '666666']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        // Report Title
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', 'DATA ANGGOTA KOPERASI');
        $sheet->getStyle('A1')->applyFromArray($titleStyle);

        // Filter Info
        $filterStrings = [];
        if ($request->search) {
            $filterStrings[] = "Pencarian: \"{$request->search}\"";
        }
        if ($request->status) {
            $filterStrings[] = 'Status: '.ucfirst($request->status);
        }
        if ($request->department) {
            $filterStrings[] = 'Departemen: '.$request->department;
        }

        $sheet->mergeCells('A2:L2');
        $infoText = empty($filterStrings) ? 'Semua Data' : implode(' | ', $filterStrings);
        $sheet->setCellValue('A2', $infoText.' | Diunduh: '.date('d/m/Y H:i'));
        $sheet->getStyle('A2')->applyFromArray($subtitleStyle);

        // Empty row
        $sheet->setCellValue('A3', '');

        // Column Headers (at row 4)
        $headers = [
            'No', 'ID Anggota', 'NIK', 'Nama Lengkap', 'Email',
            'Departemen', 'Jabatan', 'Tanggal Bergabung', 'Status',
            'Limit Kredit (Rp)', 'Total Poin', 'Saldo Simpanan (Rp)',
        ];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col.'4', $header);
            $col++;
        }
        $sheet->getStyle('A4:L4')->applyFromArray($headerStyle);

        // Data (starting at row 5)
        $row = 5;
        foreach ($members as $index => $m) {
            $sheet->setCellValue('A'.$row, $index + 1);
            $sheet->setCellValue('B'.$row, $m->member_id);
            $sheet->setCellValue('C'.$row, $m->employee_id ?? '-');
            $sheet->setCellValue('D'.$row, $m->user->name ?? '-');
            $sheet->setCellValue('E'.$row, $m->user->email ?? '-');
            $sheet->setCellValue('F'.$row, $m->department ?? '-');
            $sheet->setCellValue('G'.$row, $m->position ?? '-');
            $sheet->setCellValue('H'.$row, $m->join_date ? $m->join_date->format('d/m/Y') : '-');
            $sheet->setCellValue('I'.$row, ucfirst($m->status));
            $sheet->setCellValue('J'.$row, $m->credit_limit);
            $sheet->setCellValue('K'.$row, $m->points);

            // Basic savings info if available
            $savingsBalance = \App\Models\Saving::where('member_id', $m->id)->sum('amount');
            $sheet->setCellValue('L'.$row, $savingsBalance);

            $row++;
        }

        // Format amount column
        $sheet->getStyle('J5:L'.($row - 1))->getNumberFormat()->setFormatCode('#,##0');

        // Auto-size columns
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add borders to data
        $sheet->getStyle('A4:L'.($row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Download
        $filename = 'Daftar_Anggota_'.date('Y-m-d_His').'.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        \App\Models\AuditLog::log(
            'export',
            'Mengekspor data anggota ke Excel'.(count($filterStrings) > 0 ? ' (Filter: '.implode(', ', $filterStrings).')' : '')
        );

        $writer->save('php://output');
        exit;
    }

    /**
     * Show the form for creating a new member.
     */
    public function create()
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action. Only system administrators can register new members.');
        }

        $departments = \App\Models\Department::active()->orderBy('name')->get();
        $positions = \App\Models\Position::active()->orderBy('name')->get();

        return view('members.create', compact('departments', 'positions'));
    }

    /**
     * Store a newly created member in storage.
     */
    public function store(MemberRequest $request)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action. Only system administrators can register new members.');
        }
        try {
            DB::beginTransaction();

            // Create User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'member',
                'phone' => $request->phone,
                'is_active' => true,
            ]);

            // Handle photo upload
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('members', 'public');
            }

            // Get default credit limit from settings
            $defaultCreditLimit = \App\Models\Setting::where('key', 'default_credit_limit')->value('value') ?? 500000;

            // Create Member Profile
            $member = Member::create([
                'user_id' => $user->id,
                'member_id' => Member::generateMemberId(),
                'employee_id' => $request->employee_id,
                'department' => $request->department,
                'position' => $request->position,
                'join_date' => $request->join_date,
                'status' => 'active',
                'credit_limit' => $defaultCreditLimit,
                'address' => $request->address,
                'id_card_number' => $request->id_card_number,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'photo' => $photoPath,
            ]);

            \App\Models\AuditLog::log(
                'create',
                "Menambahkan anggota baru: {$user->name} ({$member->member_id})",
                $member
            );

            DB::commit();

            return redirect()->route('members.show', $member)
                ->with('success', 'Anggota berhasil ditambahkan dengan ID: '.$member->member_id);

        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded photo if exists
            if (isset($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan anggota: '.$e->getMessage());
        }
    }

    /**
     * Display the specified member.
     */
    public function show(Member $member)
    {
        $user = auth()->user();
        
        // Allow if user has admin access
        $isAdmin = $user->hasAdminAccess();
        
        // Allow if this is the user's own member profile
        $isOwnProfile = $user->id === $member->user_id;
        
        // Alternative check via member relation
        $isOwnMember = $user->member && $user->member->id === $member->id;
        
        if (! $isAdmin && ! $isOwnProfile && ! $isOwnMember) {
            abort(403, 'Unauthorized action.');
        }
        $member->load(['user', 'savings', 'loans.payments']);

        // Shopping Credit Stats
        $unpaidCredit = \App\Models\Transaction::where('user_id', $member->user_id)
            ->where('payment_method', 'kredit')
            ->where('status', 'credit')
            ->sum('total_amount');

        // Statistics
        $stats = [
            'total_savings' => $member->total_simpanan,
            'total_deposits' => $member->savings()->where('transaction_type', 'deposit')->sum('amount'),
            'total_withdrawals' => $member->savings()->where('transaction_type', 'withdrawal')->sum('amount'),
            'active_loans' => $member->loans()->where('status', 'active')->count(),
            'total_loan_amount' => $member->loans()->where('status', 'active')->sum('remaining_amount'),
            'unpaid_credit' => $unpaidCredit,
        ];

        // Recent activities
        $recentSavings = $member->savings()->latest('transaction_date')->take(5)->get();
        $activeLoans = $member->loans()->whereIn('status', ['active', 'approved'])->get();
        $recentCredits = \App\Models\Transaction::where('user_id', $member->user_id)
            ->where('payment_method', 'kredit')
            ->where('status', 'credit')
            ->latest()
            ->take(5)
            ->get();

        return view('members.show', compact('member', 'stats', 'recentSavings', 'activeLoans', 'recentCredits'));
    }

    /**
     * Show the form for editing the specified member.
     */
    public function edit(Member $member)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action. Only system administrators can edit member data.');
        }

        $member->load('user');

        return view('members.edit', compact('member'));
    }

    /**
     * Update the specified member in storage.
     */
    public function update(MemberRequest $request, Member $member)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action. Only system administrators can update member data.');
        }

        try {
            DB::beginTransaction();

            // Update User
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $member->user->update($userData);

            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($member->photo) {
                    Storage::disk('public')->delete($member->photo);
                }
                $photoPath = $request->file('photo')->store('members', 'public');
            } else {
                $photoPath = $member->photo;
            }

            // Update Member Profile
            $member->update([
                'employee_id' => $request->employee_id,
                'department' => $request->department,
                'position' => $request->position,
                'join_date' => $request->join_date,
                'credit_limit' => $request->credit_limit ?? 500000,
                'address' => $request->address,
                'id_card_number' => $request->id_card_number,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'photo' => $photoPath,
            ]);

            \App\Models\AuditLog::log(
                'update',
                "Memperbarui data anggota: {$member->user->name} ({$member->member_id})",
                $member
            );

            DB::commit();

            return redirect()->route('members.show', $member)
                ->with('success', 'Data anggota berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified member from storage.
     */
    public function destroy(Member $member)
    {
        \Illuminate\Support\Facades\Gate::authorize('delete-data');

        try {
            // Check if member has active loans
            if ($member->loans()->where('status', 'active')->exists()) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menghapus anggota yang memiliki pinjaman aktif');
            }

            // Check if member has positive savings balance
            $savingsBalance = \App\Models\Saving::where('member_id', $member->id)
                ->selectRaw("SUM(CASE WHEN transaction_type = 'deposit' THEN amount ELSE -amount END) as balance")
                ->value('balance') ?? 0;
            if ($savingsBalance > 0) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menghapus anggota yang masih memiliki saldo simpanan (Rp '.number_format($savingsBalance, 0, ',', '.').')');
            }

            // Check if member has outstanding Kredit Mart
            $outstandingCredit = \App\Models\Transaction::where('user_id', $member->user_id)
                ->where('status', 'credit')
                ->sum('total_amount');
            if ($outstandingCredit > 0) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menghapus anggota yang masih memiliki hutang Kredit Mart (Rp '.number_format($outstandingCredit, 0, ',', '.').')');
            }

            DB::beginTransaction();

            // Delete photo
            if ($member->photo) {
                Storage::disk('public')->delete($member->photo);
            }

            $memberName = $member->user->name;
            $memberId = $member->member_id;

            // Delete member (will cascade to user because of onDelete cascade)
            $member->user->delete();

            \App\Models\AuditLog::log(
                'delete',
                "Menghapus anggota: {$memberName} ({$memberId})"
            );

            DB::commit();

            return redirect()->route('members.index')
                ->with('success', "Anggota {$memberName} ({$memberId}) berhasil dihapus");

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal menghapus anggota: '.$e->getMessage());
        }
    }

    /**
     * Remove multiple members from storage.
     */
    public function bulkDestroy(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('delete-data');

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:members,id',
        ]);

        try {
            $count = 0;
            $failed = 0;

            DB::beginTransaction();

            foreach ($request->ids as $id) {
                $member = Member::find($id);

                // Skip if member has active loans
                if ($member->loans()->where('status', 'active')->exists()) {
                    $failed++;

                    continue;
                }

                // Skip if member has positive savings balance
                $savingsBalance = \App\Models\Saving::where('member_id', $member->id)
                    ->selectRaw("SUM(CASE WHEN transaction_type = 'deposit' THEN amount ELSE -amount END) as balance")
                    ->value('balance') ?? 0;
                if ($savingsBalance > 0) {
                    $failed++;

                    continue;
                }

                // Skip if member has outstanding Kredit Mart
                $outstandingCredit = \App\Models\Transaction::where('user_id', $member->user_id)
                    ->where('status', 'credit')
                    ->sum('total_amount');
                if ($outstandingCredit > 0) {
                    $failed++;

                    continue;
                }

                if ($member->photo) {
                    Storage::disk('public')->delete($member->photo);
                }

                $member->user->delete(); // Cascades delete
                $count++;
            }

            DB::commit();

            $message = "Berhasil menghapus {$count} anggota.";
            if ($failed > 0) {
                $message .= " ({$failed} anggota gagal dihapus karena memiliki pinjaman aktif, saldo simpanan, atau hutang kredit).";
            }

            \App\Models\AuditLog::log(
                'delete',
                "Menghapus {$count} anggota secara massal".($failed > 0 ? " ({$failed} gagal)" : '')
            );

            return redirect()->back()->with($failed > 0 ? 'warning' : 'success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal menghapus data: '.$e->getMessage());
        }
    }

    /**
     * Toggle member status (active/inactive).
     */
    public function toggleStatus(Member $member)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        $newStatus = $member->status === 'active' ? 'inactive' : 'active';

        DB::transaction(function () use ($member, $newStatus) {
            // Jika mengaktifkan member dan ID masih pending (REG-), generate ID baru
            if ($newStatus === 'active' && str_starts_with($member->member_id, 'REG-')) {
                $member->member_id = Member::generateMemberId();
            }

            $member->update(['status' => $newStatus]);
            $member->user->update(['is_active' => $newStatus === 'active']);

            \App\Models\AuditLog::log(
                'update',
                "Mengubah status anggota {$member->user->name} menjadi ".ucfirst($newStatus),
                $member
            );
        });

        return redirect()->back()
            ->with('success', 'Status anggota berhasil diubah menjadi '.ucfirst($newStatus));
    }

    /**
     * Print member card.
     */
    public function printCard(Member $member)
    {
        $user = auth()->user();
        $isAdmin = $user->hasAdminAccess();
        $isOwnProfile = $user->id === $member->user_id;
        $isOwnMember = $user->member && $user->member->id === $member->id;
        
        if (! $isAdmin && ! $isOwnProfile && ! $isOwnMember) {
            abort(403, 'Unauthorized action.');
        }
        $member->load('user');

        // Generate document record for verification
        $documentData = [
            'type' => 'Member Card',
            'member_id' => $member->member_id,
            'member_name' => $member->user->name ?? '-',
            'nik' => $member->nik ?? '-',
            'join_date' => $member->join_date,
            'status' => $member->status,
        ];

        // Check if document already exists for this member
        $generatedDocument = \App\Models\GeneratedDocument::where('reference_type', Member::class)
            ->where('reference_id', $member->id)
            ->where('document_type', 'Member Card')
            ->first();

        if (! $generatedDocument) {
            // Generate document number: KTA-001/I/2026
            $month = now()->month;
            $year = now()->year;
            $monthRoman = $this->numberToRoman($month);

            $existingCount = \App\Models\GeneratedDocument::where('document_type', 'Member Card')
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->count();

            $seq = str_pad($existingCount + 1, 3, '0', STR_PAD_LEFT);
            $documentNumber = "KTA-{$seq}/{$monthRoman}/{$year}";

            $generatedDocument = \App\Models\GeneratedDocument::create([
                'document_type' => 'Member Card',
                'document_number' => $documentNumber,
                'data' => $documentData,
                'generated_by' => auth()->id(),
                'reference_type' => Member::class,
                'reference_id' => $member->id,
            ]);
        }

        // Generate QR Code with Member ID (POS-compatible)
        $qrContent = $member->member_id;
        $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=100x100&data='.urlencode($qrContent);

        try {
            $qrImageData = @file_get_contents($qrApiUrl);
            $qrCode = $qrImageData ? 'data:image/png;base64,'.base64_encode($qrImageData) : null;
        } catch (\Exception $e) {
            $qrCode = null;
        }

        return view('members.card', compact('member', 'generatedDocument', 'qrCode'));
    }

    /**
     * Show digital member card (mobile friendly).
     */
    public function digitalCard(Member $member)
    {
        $user = auth()->user();
        $isAdmin = $user->hasAdminAccess();
        $isOwnProfile = $user->id === $member->user_id;
        $isOwnMember = $user->member && $user->member->id === $member->id;
        
        if (! $isAdmin && ! $isOwnProfile && ! $isOwnMember) {
            abort(403, 'Unauthorized action.');
        }
        $member->load('user');

        // Use the same generated document as printCard
        $generatedDocument = \App\Models\GeneratedDocument::where('reference_type', Member::class)
            ->where('reference_id', $member->id)
            ->where('document_type', 'Member Card')
            ->first();

        if (! $generatedDocument) {
            // Generate document record (same as printCard)
            $documentData = [
                'type' => 'Member Card',
                'member_id' => $member->member_id,
                'member_name' => $member->user->name ?? '-',
                'nik' => $member->nik ?? '-',
                'join_date' => $member->join_date,
                'status' => $member->status,
            ];

            $month = now()->month;
            $year = now()->year;
            $monthRoman = $this->numberToRoman($month);

            $existingCount = \App\Models\GeneratedDocument::where('document_type', 'Member Card')
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->count();

            $seq = str_pad($existingCount + 1, 3, '0', STR_PAD_LEFT);
            $documentNumber = "KTA-{$seq}/{$monthRoman}/{$year}";

            $generatedDocument = \App\Models\GeneratedDocument::create([
                'document_type' => 'Member Card',
                'document_number' => $documentNumber,
                'data' => $documentData,
                'generated_by' => auth()->id(),
                'reference_type' => Member::class,
                'reference_id' => $member->id,
            ]);
        }

        // Generate QR Code with Member ID (POS-compatible)
        $qrContent = $member->member_id;
        $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data='.urlencode($qrContent);

        try {
            $qrImageData = @file_get_contents($qrApiUrl);
            $qrCode = $qrImageData ? 'data:image/png;base64,'.base64_encode($qrImageData) : null;
        } catch (\Exception $e) {
            $qrCode = null;
        }

        return view('members.digital_card', compact('member', 'generatedDocument', 'qrCode'));
    }

    /**
     * Convert number to Roman numeral (for document numbering)
     */
    private function numberToRoman($number)
    {
        $map = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

        return $map[$number - 1] ?? (string) $number;
    }

    /**
     * API Search for POS Member lookup
     */
    public function apiSearch(Request $request)
    {
        $q = trim($request->get('q'));

        if (! $q || strlen($q) < 2) {
            return response()->json(['member' => null, 'matches' => []]);
        }

        // Search member by member_id, employee_id, or name
        $query = Member::with('user')
            ->where(function ($query) use ($q) {
                $query->where('member_id', $q)
                    ->orWhere('member_id', 'like', '%'.$q.'%')
                    ->orWhere('employee_id', $q)
                    ->orWhereHas('user', function ($userQuery) use ($q) {
                        $userQuery->where('name', 'like', '%'.$q.'%');
                    });
            });

        $matches = $query->take(10)->get();
        $member = $matches->count() === 1 ? $matches->first() : null; // exact match or single result behavior?

        // If query matches exact ID, prioritize it
        $exactMatch = $matches->firstWhere('member_id', $q) ?? $matches->firstWhere('employee_id', $q);
        if ($exactMatch) {
            $member = $exactMatch;
        }

        // Format matches for frontend
        $formattedMatches = $matches->map(function ($m) {
            return [
                'id' => $m->id,
                'member_id' => $m->member_id,
                'name' => $m->user->name ?? 'Unknown',
                'credit_limit' => $m->credit_limit ?? 500000,
            ];
        });

        // If we selected a single member (exact match), get extra details
        $responseData = ['member' => null, 'matches' => $formattedMatches];

        if ($member) {
            // Get voluntary savings balance
            $balance = \App\Models\Saving::where('member_id', $member->id)
                ->where('type', 'sukarela')
                ->sum('amount');

            // Get current outstanding credit
            $creditUsed = \App\Models\Transaction::where('user_id', $member->user_id)
                ->where('payment_method', 'kredit')
                ->where('status', 'credit')
                ->sum('total_amount');

            $creditLimit = $member->credit_limit ?? 500000;
            $creditAvailable = max(0, $creditLimit - $creditUsed);

            $responseData['member'] = [
                'id' => $member->id,
                'member_id' => $member->member_id,
                'name' => $member->user->name ?? 'Unknown',
                'balance' => $balance,
                'credit_limit' => $creditLimit,
                'credit_used' => $creditUsed,
                'credit_available' => $creditAvailable,
            ];
        }

        return response()->json($responseData);
    }

    /**
     * Show credit history for the logged in member.
     */
    public function myCredits()
    {
        $user = auth()->user();
        $member = $user->member;

        if (! $member) {
            return redirect()->route('dashboard')->with('error', 'Data anggota tidak ditemukan.');
        }

        $transactions = \App\Models\Transaction::with(['items.product'])
            ->where('user_id', $user->id)
            ->where('payment_method', 'kredit')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $creditUsed = \App\Models\Transaction::where('user_id', $user->id)
            ->where('payment_method', 'kredit')
            ->where('status', 'credit')
            ->sum('total_amount');

        $creditAvailable = max(0, ($member->credit_limit ?? 500000) - $creditUsed);

        return view('members.credits', compact('member', 'transactions', 'creditUsed', 'creditAvailable'));
    }

    /**
     * Print transaction history (rekening koran belanja) for a member.
     */
    public function printTransactionHistory(Member $member, Request $request)
    {
        // Authorization check
        if (! auth()->user()->hasAdminAccess() && auth()->id() !== $member->user_id) {
            abort(403);
        }

        $member->load('user');

        // Get date range from request or default to last 3 months
        $startDate = $request->start_date
            ? \Carbon\Carbon::parse($request->start_date)
            : now()->subMonths(3)->startOfMonth();
        $endDate = $request->end_date
            ? \Carbon\Carbon::parse($request->end_date)
            : now()->endOfDay();

        // Get all transactions for this member within date range
        $transactions = \App\Models\Transaction::with(['items.product', 'cashier'])
            ->where('user_id', $member->user_id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'asc')
            ->get();

        // Calculate totals
        $totalSpent = $transactions->where('status', '!=', 'cancelled')->sum('total_amount');
        $totalPaid = $transactions->where('status', '!=', 'cancelled')->sum('paid_amount');
        $totalCredit = $transactions->where('payment_method', 'kredit')->where('status', 'credit')->sum('total_amount');

        return view('members.print_transactions', compact(
            'member',
            'transactions',
            'startDate',
            'endDate',
            'totalSpent',
            'totalPaid',
            'totalCredit'
        ));
    }
}
