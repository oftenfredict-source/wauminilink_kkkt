<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BranchAccess
{
    /**
     * Handle an incoming request.
     * Ensures branch users can only access their branch data
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Usharika admins and main campus users have full access
        if ($user->isUsharikaAdmin() || ($user->getCampus() && $user->getCampus()->is_main_campus)) {
            return $next($request);
        }

        // Branch users - access is controlled at controller level
        // This middleware just ensures user has a valid campus
        if ($user->isBranchUser()) {
            $campus = $user->getCampus();
            if (!$campus) {
                abort(403, 'You must be assigned to a branch to access this resource.');
            }
        }

        return $next($request);
    }
}














