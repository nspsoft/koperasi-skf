<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileComplete
{
    /**
     * Required fields for a complete profile
     */
    protected array $requiredFields = [
        'photo',
        'gender',
        'birth_date',
        'id_card_number',
        'address',
        'employee_id',
        'department',
        'position',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Skip if not authenticated
        if (!$user) {
            return $next($request);
        }

        // Skip for admin users
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Skip if already on profile completion page
        if ($request->routeIs('profile.complete') || $request->routeIs('profile.complete.update')) {
            return $next($request);
        }

        // Skip for logout route
        if ($request->routeIs('logout')) {
            return $next($request);
        }

        // Check if user has member record
        $member = $user->member;
        if (!$member) {
            return $next($request);
        }

        // Check if profile is complete
        if (!$this->isProfileComplete($member)) {
            return redirect()->route('profile.complete')
                ->with('warning', 'Silakan lengkapi profil Anda terlebih dahulu untuk melanjutkan.');
        }

        return $next($request);
    }

    /**
     * Check if all required fields are filled
     */
    protected function isProfileComplete($member): bool
    {
        foreach ($this->requiredFields as $field) {
            if (empty($member->{$field})) {
                return false;
            }
        }
        return true;
    }
}
