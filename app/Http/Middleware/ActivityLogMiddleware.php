<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\ActivityLog;
use App\Models\SystemLog;
use App\Services\DeviceInfoService;
use Symfony\Component\HttpFoundation\Response;

class ActivityLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log authenticated requests
        if (Auth::check()) {
            $this->logActivity($request, $response);
        }

        return $response;
    }

    /**
     * Log the activity
     */
    protected function logActivity(Request $request, Response $response): void
    {
        $user = Auth::user();
        $route = $request->route();
        $routeName = $route ? $route->getName() : null;
        
        // Skip logging for certain routes (like AJAX requests, API calls, etc.)
        $skipRoutes = ['logout', 'login.post'];
        if ($routeName && in_array($routeName, $skipRoutes)) {
            return;
        }

        // Determine action based on HTTP method and route
        $action = $this->determineAction($request);
        
        // Skip if action is not significant
        if (!$action || $action === 'view') {
            // Only log views for important pages
            $importantViews = ['dashboard', 'members', 'leaders', 'finance', 'settings'];
            if (!$routeName || !str_contains($routeName, implode('|', $importantViews))) {
                return;
            }
        }

        try {
            $deviceInfo = DeviceInfoService::getDeviceInfo($request);
            
            // Check if table exists before trying to log
            if (\Schema::hasTable('activity_logs')) {
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => $action ?? 'view',
                    'description' => $this->generateDescription($request, $action),
                    'ip_address' => $deviceInfo['ip_address'],
                    'user_agent' => $deviceInfo['user_agent'],
                    'route' => $routeName ?? $request->path(),
                    'method' => $request->method(),
                ]);
            }

            // Also log to system logs for important actions
            if (\Schema::hasTable('system_logs') && in_array($action, ['create', 'update', 'delete', 'approve', 'login', 'logout'])) {
                SystemLog::create([
                    'user_id' => $user->id,
                    'level' => 'info',
                    'category' => 'application',
                    'action' => $action ?? 'view',
                    'message' => $this->generateDescription($request, $action),
                    'ip_address' => $deviceInfo['ip_address'],
                    'user_agent' => $deviceInfo['user_agent'],
                    'device_type' => $deviceInfo['device_type'],
                    'browser' => $deviceInfo['browser'],
                    'os' => $deviceInfo['os'],
                    'device_name' => $deviceInfo['device_name'],
                    'mac_address' => $deviceInfo['mac_address'],
                    'screen_resolution' => $deviceInfo['screen_resolution'],
                    'timezone' => $deviceInfo['timezone'],
                    'language' => $deviceInfo['language'],
                    'device_properties' => $deviceInfo['device_properties'],
                    'route' => $routeName ?? $request->path(),
                    'method' => $request->method(),
                ]);
            }
        } catch (\Exception $e) {
            // Silently fail logging to not break the application
            // This allows the app to work even if migrations haven't been run
        }
    }

    /**
     * Determine the action based on request
     */
    protected function determineAction(Request $request): ?string
    {
        $method = $request->method();
        $routeName = $request->route()?->getName() ?? '';

        // Map HTTP methods to actions
        if ($method === 'POST') {
            if (str_contains($routeName, 'store') || str_contains($routeName, 'create')) {
                return 'create';
            }
            if (str_contains($routeName, 'approve')) {
                return 'approve';
            }
            if (str_contains($routeName, 'deactivate') || str_contains($routeName, 'delete')) {
                return 'delete';
            }
            return 'update';
        }

        if ($method === 'PUT' || $method === 'PATCH') {
            return 'update';
        }

        if ($method === 'DELETE') {
            return 'delete';
        }

        if ($method === 'GET') {
            return 'view';
        }

        return null;
    }

    /**
     * Generate description for the activity
     */
    protected function generateDescription(Request $request, ?string $action): string
    {
        $routeName = $request->route()?->getName() ?? $request->path();
        $user = Auth::user();
        
        $descriptions = [
            'login' => "User logged in",
            'logout' => "User logged out",
            'create' => "Created new record via {$routeName}",
            'update' => "Updated record via {$routeName}",
            'delete' => "Deleted record via {$routeName}",
            'approve' => "Approved record via {$routeName}",
            'view' => "Viewed {$routeName}",
        ];

        return $descriptions[$action] ?? "Performed {$action} on {$routeName}";
    }
}

