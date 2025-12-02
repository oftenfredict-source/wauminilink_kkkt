<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionRevoked
{
    /**
     * Handle an incoming request.
     * Check if the current session has been revoked (deleted from database)
     * If revoked, force logout the user
     * Also checks if user is blocked from logging in
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check for authenticated users
        if (Auth::check() && $request->hasSession()) {
            $sessionId = $request->session()->getId();
            $user = Auth::user();
            
            // First, check if user is blocked from logging in
            if ($user && $user->login_blocked_until) {
                // Get raw timestamp from database to avoid timezone conversion issues
                $rawBlockedUntil = \Illuminate\Support\Facades\DB::table('users')
                    ->where('id', $user->id)
                    ->value('login_blocked_until');
                
                if ($rawBlockedUntil) {
                    // Parse as UTC directly (how it's stored in database)
                    $blockedUntil = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $rawBlockedUntil, 'UTC');
                    $now = \Carbon\Carbon::now('UTC');
                    
                    // If block has expired, clear it
                    if ($blockedUntil->lte($now)) {
                        $user->update(['login_blocked_until' => null]);
                        $user->refresh();
                    } else {
                        // User is still blocked, force logout
                        Auth::logout();
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();
                        
                        // Convert to Tanzania timezone for display
                        $tanzaniaTimezone = 'Africa/Dar_es_Salaam';
                        $blockedUntilDisplay = $blockedUntil->copy()->setTimezone($tanzaniaTimezone);
                        $unblockTime = $blockedUntilDisplay->format('F j, Y \a\t g:i A');
                        return redirect()->route('login')
                            ->with('error', "Your account has been blocked. You can login again on {$unblockTime}.");
                    }
                }
            }
            
            // Check if session exists in database
            // When admin revokes a session, it's deleted from the database
            // This check will detect revoked sessions
            $sessionExists = DB::table('sessions')->where('id', $sessionId)->exists();
            
            if (!$sessionExists) {
                // Session has been revoked (deleted by admin), force logout
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Redirect to login with message
                return redirect()->route('login')
                    ->with('error', 'Your session has been revoked by an administrator. Please login again.');
            }
        }

        return $next($request);
    }
}

