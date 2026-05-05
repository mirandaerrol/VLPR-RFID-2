<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\DutyAssignment;
use App\Models\OffDutyAccessLog;
use Carbon\Carbon;

class CheckGuardDuty
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->role === 'guard') {
            $now = Carbon::now();
            $today = $now->toDateString();
            $currentTime = $now->toTimeString();

            $onDuty = DutyAssignment::where('user_id', $user->id)
                ->where('duty_date', $today)
                ->where('shift_start', '<=', $currentTime)
                ->where('shift_end', '>=', $currentTime)
                ->exists();

            if (!$onDuty) {
                // Log the unauthorized attempt
                OffDutyAccessLog::create([
                    'user_id' => $user->id,
                    'attempted_at' => now(),
                    'ip_address' => $request->ip(),
                ]);

                // If it's an AJAX request (like RFID scan), return JSON
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your shift has ended or you are not on duty today.'
                    ], 403);
                }

                // Otherwise, logout and redirect
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'Your shift has ended or you are not on duty today.');
            }
        }

        return $next($request);
    }
}
