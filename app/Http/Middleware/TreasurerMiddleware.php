<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TreasurerMiddleware
{
    /**
     * Handle an incoming request.
     * 
     * This middleware restricts treasurer users to only access finance-related routes.
     * If a treasurer tries to access non-finance routes, they will be redirected to the finance dashboard.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        // Block members from accessing general finance routes (they should use member.finance)
        if ($user->isMember()) {
            $currentRoute = $request->route()?->getName() ?? '';
            $currentPath = $request->path();
            
            // Allow member routes, but block general finance routes
            $isMemberRoute = ($currentRoute && str_starts_with($currentRoute, 'member.')) ||
                             str_starts_with($currentPath, 'member/');
            
            $isGeneralFinanceRoute = ($currentRoute && str_starts_with($currentRoute, 'finance.')) ||
                                     str_starts_with($currentPath, 'finance/');
            
            // If trying to access general finance routes, redirect to member dashboard
            if ($isGeneralFinanceRoute && !$isMemberRoute) {
                return redirect()->route('member.dashboard')
                    ->with('error', 'You can only view your own finance records. Please use "My Finance" from the member menu.');
            }
        }
        
        // If user is treasurer, only allow finance routes
        if ($user->isTreasurer()) {
            // Check if the current route is a finance route
            $currentRoute = $request->route()?->getName() ?? '';
            $currentPath = $request->path();
            
            // Allow finance routes, reports routes, dashboard route, password change route, and attendance routes
            $isFinanceRoute = ($currentRoute && str_starts_with($currentRoute, 'finance.')) || 
                             ($currentRoute && str_starts_with($currentRoute, 'reports.')) ||
                             ($currentRoute && str_starts_with($currentRoute, 'attendance.')) ||
                             ($currentRoute && str_starts_with($currentRoute, 'biometric.')) ||
                             ($currentRoute && str_starts_with($currentRoute, 'leader.change-password')) ||
                             ($currentRoute && str_starts_with($currentRoute, 'leader.password.update')) ||
                             str_starts_with($currentPath, 'finance/') ||
                             str_starts_with($currentPath, 'reports/') ||
                             str_starts_with($currentPath, 'attendance/') ||
                             str_starts_with($currentPath, 'biometric/') ||
                             str_starts_with($currentPath, 'leader/change-password') ||
                             $currentRoute === 'dashboard' ||
                             $currentRoute === 'finance.dashboard' ||
                             $currentPath === 'dashboard';
            
            // If not a finance route, redirect to finance dashboard
            if (!$isFinanceRoute) {
                return redirect()->route('finance.dashboard')
                    ->with('error', 'You only have access to finance-related features.');
            }
        }
        
        return $next($request);
    }
}

