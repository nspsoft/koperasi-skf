<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('date', Carbon::today())
            ->first();

        $history = Attendance::where('user_id', $user->id)
            ->latest('date')
            ->paginate(10);

        return view('attendance.index', compact('todayAttendance', 'history'));
    }

    public function clockIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'notes' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();
        $date = Carbon::today();

        // Cek apakah sudah absen masuk hari ini
        $existing = Attendance::where('user_id', $user->id)
            ->where('date', $date)
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah melakukan absen masuk hari ini.');
        }

        // Validasi radius
        $storeLat = Setting::where('key', 'store_latitude')->value('value') ?? -6.200000; // Default jika belum diset
        $storeLong = Setting::where('key', 'store_longitude')->value('value') ?? 106.816666;
        $radius = Setting::where('key', 'store_radius')->value('value') ?? 100; // dalam meter

        $distance = $this->calculateDistance($request->latitude, $request->longitude, $storeLat, $storeLong);

        if ($distance > $radius) {
            return back()->with('error', 'Anda berada di luar radius toko (Jarak: ' . round($distance) . ' meter).');
        }

        Attendance::create([
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => Carbon::now(),
            'lat_in' => $request->latitude,
            'long_in' => $request->longitude,
            'status' => 'present',
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Berhasil absen masuk!');
    }

    public function clockOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = auth()->user();
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', Carbon::today())
            ->first();

        if (!$attendance) {
            return back()->with('error', 'Anda belum melakukan absen masuk hari ini.');
        }

        if ($attendance->clock_out) {
            return back()->with('error', 'Anda sudah melakukan absen pulang hari ini.');
        }

        // Validasi radius
        $storeLat = Setting::where('key', 'store_latitude')->value('value') ?? -6.200000;
        $storeLong = Setting::where('key', 'store_longitude')->value('value') ?? 106.816666;
        $radius = Setting::where('key', 'store_radius')->value('value') ?? 100;

        $distance = $this->calculateDistance($request->latitude, $request->longitude, $storeLat, $storeLong);

        if ($distance > $radius) {
            return back()->with('error', 'Anda berada di luar radius toko (Jarak: ' . round($distance) . ' meter).');
        }

        $attendance->update([
            'clock_out' => Carbon::now(),
            'lat_out' => $request->latitude,
            'long_out' => $request->longitude,
        ]);

        return back()->with('success', 'Berhasil absen pulang!');
    }

    /**
     * Menghitung jarak antara dua titik koordinat (Haversine formula)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // dalam meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
