<?php

namespace App\Services;

use Illuminate\Http\Request;

class DeviceInfoService
{
    /**
     * Extract device information from request
     */
    public static function getDeviceInfo(Request $request): array
    {
        $userAgent = $request->userAgent() ?? '';
        
        return [
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent,
            'device_type' => self::getDeviceType($userAgent),
            'browser' => self::getBrowser($userAgent),
            'os' => self::getOperatingSystem($userAgent),
            'device_name' => self::getDeviceName($userAgent),
            'mac_address' => self::getMacAddress($request),
            'screen_resolution' => $request->header('X-Screen-Resolution'),
            'timezone' => $request->header('X-Timezone') ?? date_default_timezone_get(),
            'language' => $request->getPreferredLanguage() ?? $request->header('Accept-Language'),
            'device_properties' => [
                'platform' => self::getPlatform($userAgent),
                'is_mobile' => self::isMobile($userAgent),
                'is_tablet' => self::isTablet($userAgent),
                'is_desktop' => self::isDesktop($userAgent),
                'is_bot' => self::isBot($userAgent),
            ],
        ];
    }

    /**
     * Get device type
     */
    private static function getDeviceType(string $userAgent): string
    {
        if (self::isMobile($userAgent)) {
            return 'mobile';
        } elseif (self::isTablet($userAgent)) {
            return 'tablet';
        } elseif (self::isDesktop($userAgent)) {
            return 'desktop';
        }
        return 'unknown';
    }

    /**
     * Get browser name
     */
    private static function getBrowser(string $userAgent): ?string
    {
        if (preg_match('/Chrome\/([0-9.]+)/i', $userAgent, $matches)) {
            return 'Chrome ' . $matches[1];
        } elseif (preg_match('/Firefox\/([0-9.]+)/i', $userAgent, $matches)) {
            return 'Firefox ' . $matches[1];
        } elseif (preg_match('/Safari\/([0-9.]+)/i', $userAgent, $matches)) {
            return 'Safari ' . $matches[1];
        } elseif (preg_match('/Edge\/([0-9.]+)/i', $userAgent, $matches)) {
            return 'Edge ' . $matches[1];
        } elseif (preg_match('/Opera\/([0-9.]+)/i', $userAgent, $matches)) {
            return 'Opera ' . $matches[1];
        }
        return 'Unknown';
    }

    /**
     * Get operating system
     */
    private static function getOperatingSystem(string $userAgent): ?string
    {
        if (preg_match('/Windows NT ([0-9.]+)/i', $userAgent, $matches)) {
            $version = $matches[1];
            $versions = [
                '10.0' => 'Windows 10',
                '6.3' => 'Windows 8.1',
                '6.2' => 'Windows 8',
                '6.1' => 'Windows 7',
            ];
            return $versions[$version] ?? 'Windows ' . $version;
        } elseif (preg_match('/Mac OS X ([0-9_]+)/i', $userAgent, $matches)) {
            return 'macOS ' . str_replace('_', '.', $matches[1]);
        } elseif (preg_match('/Linux/i', $userAgent)) {
            return 'Linux';
        } elseif (preg_match('/Android ([0-9.]+)/i', $userAgent, $matches)) {
            return 'Android ' . $matches[1];
        } elseif (preg_match('/iPhone OS ([0-9_]+)/i', $userAgent, $matches)) {
            return 'iOS ' . str_replace('_', '.', $matches[1]);
        }
        return 'Unknown';
    }

    /**
     * Get device name
     */
    private static function getDeviceName(string $userAgent): ?string
    {
        if (preg_match('/iPhone/i', $userAgent)) {
            return 'iPhone';
        } elseif (preg_match('/iPad/i', $userAgent)) {
            return 'iPad';
        } elseif (preg_match('/Android/i', $userAgent)) {
            if (preg_match('/Mobile/i', $userAgent)) {
                return 'Android Phone';
            }
            return 'Android Tablet';
        }
        return 'Desktop';
    }

    /**
     * Get MAC address (Note: MAC address is not available in HTTP requests for security reasons)
     * This is a placeholder - in real scenarios, MAC addresses are not accessible via HTTP
     */
    private static function getMacAddress(Request $request): ?string
    {
        // MAC addresses are not available in HTTP requests
        // They are only available on the local network
        // This would require special network-level access
        return $request->header('X-MAC-Address'); // If provided by proxy/network equipment
    }

    /**
     * Get platform
     */
    private static function getPlatform(string $userAgent): string
    {
        if (preg_match('/Windows/i', $userAgent)) {
            return 'Windows';
        } elseif (preg_match('/Mac/i', $userAgent)) {
            return 'macOS';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            return 'Linux';
        } elseif (preg_match('/Android/i', $userAgent)) {
            return 'Android';
        } elseif (preg_match('/iPhone|iPad|iPod/i', $userAgent)) {
            return 'iOS';
        }
        return 'Unknown';
    }

    /**
     * Check if mobile device
     */
    private static function isMobile(string $userAgent): bool
    {
        return preg_match('/Mobile|Android|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i', $userAgent);
    }

    /**
     * Check if tablet device
     */
    private static function isTablet(string $userAgent): bool
    {
        return preg_match('/iPad|Android(?!.*Mobile)|Tablet/i', $userAgent);
    }

    /**
     * Check if desktop device
     */
    private static function isDesktop(string $userAgent): bool
    {
        return !self::isMobile($userAgent) && !self::isTablet($userAgent);
    }

    /**
     * Check if bot/crawler
     */
    private static function isBot(string $userAgent): bool
    {
        return preg_match('/bot|crawler|spider|crawling/i', $userAgent);
    }
}


