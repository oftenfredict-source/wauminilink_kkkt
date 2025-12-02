<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UpdateSessionUserId
{
    /**
     * Handle an incoming request.
     * Update the session record with user_id if user is authenticated
     * This middleware ensures user_id is set in the sessions table for tracking
     * Works with both file and database session drivers
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Update session with user_id if user is authenticated
        // This works with both file and database session drivers
        // For file driver, we create/update a record in the sessions table for tracking
        if (Auth::check() && $request->hasSession()) {
            try {
                $sessionId = $request->session()->getId();
                $userId = Auth::id();
                
                if (!$sessionId || !$userId) {
                    return $response;
                }
                
                // Get session data for payload (if using file driver)
                $sessionData = $request->session()->all();
                $payload = base64_encode(serialize($sessionData));
                
                // Use updateOrInsert to ensure the record exists
                // This works for both new and existing sessions
                DB::table('sessions')->updateOrInsert(
                    ['id' => $sessionId],
                    [
                        'user_id' => $userId,
                        'ip_address' => $request->ip(),
                        'user_agent' => substr((string) $request->userAgent(), 0, 500),
                        'payload' => $payload,
                        'last_activity' => time(),
                    ]
                );
            } catch (\Exception $e) {
                // Log error for debugging but don't break the request
                \Log::warning('Failed to update session user_id: ' . $e->getMessage(), [
                    'session_id' => $request->session()->getId() ?? 'none',
                    'user_id' => Auth::id() ?? 'none',
                ]);
            }
        }

        return $response;
    }
}

