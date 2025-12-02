<?php

namespace App\Services;

class SystemMonitorService
{
    /**
     * Get server system information
     */
    public static function getSystemInfo(): array
    {
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        
        try {
            return [
                'cpu' => self::getCpuInfo($isWindows),
                'memory' => self::getMemoryInfo($isWindows),
                'storage' => self::getStorageInfo($isWindows),
                'server' => self::getServerInfo(),
                'php' => self::getPhpInfo(),
            ];
        } catch (\Exception $e) {
            // Return safe defaults if anything fails
            return [
                'cpu' => ['cores' => 1, 'usage_percent' => 0, 'model' => 'Unable to detect'],
                'memory' => [
                    'total' => 0,
                    'used' => 0,
                    'free' => 0,
                    'usage_percent' => 0,
                    'total_formatted' => 'N/A',
                    'used_formatted' => 'N/A',
                    'free_formatted' => 'N/A',
                ],
                'storage' => [],
                'server' => self::getServerInfo(),
                'php' => self::getPhpInfo(),
            ];
        }
    }

    /**
     * Get CPU information
     */
    private static function getCpuInfo(bool $isWindows): array
    {
        try {
            if ($isWindows) {
                // Windows CPU info
                $cpuCount = 1;
                if (function_exists('shell_exec') && !in_array('shell_exec', explode(',', ini_get('disable_functions')))) {
                    $cpuCount = (int) @shell_exec('echo %NUMBER_OF_PROCESSORS%');
                    if (!$cpuCount) {
                        $cpuCount = 1;
                    }
                }
                
                // Get CPU usage (Windows)
                $cpuUsage = self::getWindowsCpuUsage();
                
                return [
                    'cores' => $cpuCount,
                    'usage_percent' => $cpuUsage,
                    'model' => self::getWindowsCpuModel(),
                ];
            } else {
                // Linux CPU info
                $cpuInfo = @file_get_contents('/proc/cpuinfo');
                if ($cpuInfo) {
                    preg_match_all('/^processor/m', $cpuInfo, $matches);
                    $cpuCount = count($matches[0]);
                    
                    // Get CPU usage
                    $load = function_exists('sys_getloadavg') ? @sys_getloadavg() : false;
                    $cpuUsage = $load ? round(($load[0] / max($cpuCount, 1)) * 100, 2) : 0;
                    
                    preg_match('/model name\s*:\s*(.+)/m', $cpuInfo, $matches);
                    $model = $matches[1] ?? 'Unknown';
                    
                    return [
                        'cores' => $cpuCount ?: 1,
                        'usage_percent' => min(100, $cpuUsage),
                        'model' => trim($model),
                    ];
                }
            }
        } catch (\Exception $e) {
            // Return safe defaults on error
        }
        
        return [
            'cores' => 1,
            'usage_percent' => 0,
            'model' => 'Unable to detect',
        ];
    }

    /**
     * Get Windows CPU usage
     */
    private static function getWindowsCpuUsage(): float
    {
        if (!function_exists('shell_exec') || in_array('shell_exec', explode(',', ini_get('disable_functions')))) {
            return 0.0;
        }
        
        try {
            $command = 'wmic cpu get loadpercentage /value';
            $output = @shell_exec($command);
            
            if ($output && preg_match('/LoadPercentage=(\d+)/', $output, $matches)) {
                return (float) $matches[1];
            }
            
            // Fallback: try PowerShell
            $psCommand = 'powershell "Get-Counter \'\\Processor(_Total)\\% Processor Time\' | Select-Object -ExpandProperty CounterSamples | Select-Object -ExpandProperty CookedValue"';
            $output = @shell_exec($psCommand);
            
            if ($output && is_numeric(trim($output))) {
                return (float) trim($output);
            }
        } catch (\Exception $e) {
            // Return 0 on error
        }
        
        return 0.0;
    }

    /**
     * Get Windows CPU model
     */
    private static function getWindowsCpuModel(): string
    {
        if (!function_exists('shell_exec') || in_array('shell_exec', explode(',', ini_get('disable_functions')))) {
            return 'Unable to detect';
        }
        
        try {
            $command = 'wmic cpu get name /value';
            $output = @shell_exec($command);
            
            if ($output && preg_match('/Name=(.+)/', $output, $matches)) {
                return trim($matches[1]);
            }
        } catch (\Exception $e) {
            // Return default on error
        }
        
        return 'Unable to detect';
    }

    /**
     * Get memory information
     */
    private static function getMemoryInfo(bool $isWindows): array
    {
        try {
            if ($isWindows) {
                // Windows memory info
                if (function_exists('shell_exec') && !in_array('shell_exec', explode(',', ini_get('disable_functions')))) {
                    $command = 'wmic computersystem get TotalPhysicalMemory /value';
                    $output = @shell_exec($command);
                    $totalBytes = 0;
                    
                    if ($output && preg_match('/TotalPhysicalMemory=(\d+)/', $output, $matches)) {
                        $totalBytes = (int) $matches[1];
                    }
                    
                    // Get available memory
                    $command = 'wmic OS get FreePhysicalMemory /value';
                    $output = @shell_exec($command);
                    $freeBytes = 0;
                    
                    if ($output && preg_match('/FreePhysicalMemory=(\d+)/', $output, $matches)) {
                        $freeBytes = (int) $matches[1] * 1024; // Convert KB to bytes
                    }
                    
                    if ($totalBytes > 0) {
                        $usedBytes = $totalBytes - $freeBytes;
                        $usagePercent = round(($usedBytes / $totalBytes) * 100, 2);
                        
                        return [
                            'total' => $totalBytes,
                            'used' => $usedBytes,
                            'free' => $freeBytes,
                            'usage_percent' => $usagePercent,
                            'total_formatted' => self::formatBytes($totalBytes),
                            'used_formatted' => self::formatBytes($usedBytes),
                            'free_formatted' => self::formatBytes($freeBytes),
                        ];
                    }
                }
            } else {
                // Linux memory info
                $memInfo = @file_get_contents('/proc/meminfo');
                if ($memInfo) {
                    preg_match('/MemTotal:\s+(\d+)\s+kB/', $memInfo, $matches);
                    $totalBytes = isset($matches[1]) ? (int) $matches[1] * 1024 : 0;
                    
                    preg_match('/MemAvailable:\s+(\d+)\s+kB/', $memInfo, $matches);
                    $availableBytes = isset($matches[1]) ? (int) $matches[1] * 1024 : 0;
                    
                    if (!$availableBytes) {
                        // Fallback to MemFree
                        preg_match('/MemFree:\s+(\d+)\s+kB/', $memInfo, $matches);
                        $availableBytes = isset($matches[1]) ? (int) $matches[1] * 1024 : 0;
                    }
                    
                    if ($totalBytes > 0) {
                        $usedBytes = $totalBytes - $availableBytes;
                        $usagePercent = round(($usedBytes / $totalBytes) * 100, 2);
                        
                        return [
                            'total' => $totalBytes,
                            'used' => $usedBytes,
                            'free' => $availableBytes,
                            'usage_percent' => $usagePercent,
                            'total_formatted' => self::formatBytes($totalBytes),
                            'used_formatted' => self::formatBytes($usedBytes),
                            'free_formatted' => self::formatBytes($availableBytes),
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            // Fall through to PHP fallback
        }
        
        // Fallback using PHP memory functions
        try {
            $totalBytes = memory_get_usage(true);
            $limit = ini_get('memory_limit');
            $limitBytes = self::parseMemoryLimit($limit);
            
            return [
                'total' => $limitBytes ?: $totalBytes * 10, // Estimate
                'used' => $totalBytes,
                'free' => ($limitBytes ?: $totalBytes * 10) - $totalBytes,
                'usage_percent' => $limitBytes > 0 ? round(($totalBytes / $limitBytes) * 100, 2) : 0,
                'total_formatted' => self::formatBytes($limitBytes ?: $totalBytes * 10),
                'used_formatted' => self::formatBytes($totalBytes),
                'free_formatted' => self::formatBytes(($limitBytes ?: $totalBytes * 10) - $totalBytes),
            ];
        } catch (\Exception $e) {
            // Ultimate fallback
            return [
                'total' => 0,
                'used' => 0,
                'free' => 0,
                'usage_percent' => 0,
                'total_formatted' => 'N/A',
                'used_formatted' => 'N/A',
                'free_formatted' => 'N/A',
            ];
        }
    }

    /**
     * Get storage information
     */
    private static function getStorageInfo(bool $isWindows): array
    {
        $disks = [];
        
        try {
            if ($isWindows) {
                // Windows disk info
                if (function_exists('shell_exec') && !in_array('shell_exec', explode(',', ini_get('disable_functions')))) {
                    $command = 'wmic logicaldisk get size,freespace,caption /format:list';
                    $output = @shell_exec($command);
                    
                    if ($output) {
                        $lines = explode("\n", $output);
                        $currentDisk = [];
                        
                        foreach ($lines as $line) {
                            $line = trim($line);
                            if (strpos($line, 'Caption=') === 0) {
                                if (!empty($currentDisk)) {
                                    $disks[] = $currentDisk;
                                }
                                $currentDisk = ['drive' => substr($line, 8)];
                            } elseif (strpos($line, 'Size=') === 0) {
                                $currentDisk['total'] = (int) substr($line, 5);
                            } elseif (strpos($line, 'FreeSpace=') === 0) {
                                $currentDisk['free'] = (int) substr($line, 10);
                            }
                        }
                        
                        if (!empty($currentDisk)) {
                            $disks[] = $currentDisk;
                        }
                        
                        // Calculate used space
                        foreach ($disks as &$disk) {
                            if (isset($disk['total']) && isset($disk['free'])) {
                                $disk['used'] = $disk['total'] - $disk['free'];
                                $disk['usage_percent'] = $disk['total'] > 0 
                                    ? round(($disk['used'] / $disk['total']) * 100, 2) 
                                    : 0;
                                $disk['total_formatted'] = self::formatBytes($disk['total']);
                                $disk['used_formatted'] = self::formatBytes($disk['used']);
                                $disk['free_formatted'] = self::formatBytes($disk['free']);
                            }
                        }
                    }
                }
            } else {
                // Linux disk info
                if (function_exists('shell_exec') && !in_array('shell_exec', explode(',', ini_get('disable_functions')))) {
                    $command = 'df -B1';
                    $output = @shell_exec($command);
                    
                    if ($output) {
                        $lines = explode("\n", $output);
                        foreach ($lines as $line) {
                            if (preg_match('/^\/dev\/(\S+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)%\s+(.+)$/', $line, $matches)) {
                                $disks[] = [
                                    'drive' => $matches[6],
                                    'total' => (int) $matches[2],
                                    'used' => (int) $matches[3],
                                    'free' => (int) $matches[4],
                                    'usage_percent' => (float) $matches[5],
                                    'total_formatted' => self::formatBytes((int) $matches[2]),
                                    'used_formatted' => self::formatBytes((int) $matches[3]),
                                    'free_formatted' => self::formatBytes((int) $matches[4]),
                                ];
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Fall through to fallback
        }
        
        // If no disks found, use application root
        if (empty($disks)) {
            try {
                $rootPath = base_path();
                $totalBytes = @disk_total_space($rootPath);
                $freeBytes = @disk_free_space($rootPath);
                
                if ($totalBytes && $freeBytes) {
                    $usedBytes = $totalBytes - $freeBytes;
                    
                    $disks[] = [
                        'drive' => $rootPath,
                        'total' => $totalBytes,
                        'used' => $usedBytes,
                        'free' => $freeBytes,
                        'usage_percent' => round(($usedBytes / $totalBytes) * 100, 2),
                        'total_formatted' => self::formatBytes($totalBytes),
                        'used_formatted' => self::formatBytes($usedBytes),
                        'free_formatted' => self::formatBytes($freeBytes),
                    ];
                }
            } catch (\Exception $e) {
                // Return empty array if all fails
            }
        }
        
        return $disks;
    }

    /**
     * Get server information
     */
    private static function getServerInfo(): array
    {
        return [
            'os' => PHP_OS,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'hostname' => gethostname() ?: 'Unknown',
            'ip_address' => $_SERVER['SERVER_ADDR'] ?? 'Unknown',
            'uptime' => self::getUptime(),
        ];
    }

    /**
     * Get PHP information
     */
    private static function getPhpInfo(): array
    {
        return [
            'version' => PHP_VERSION,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'timezone' => date_default_timezone_get(),
        ];
    }

    /**
     * Get system uptime
     */
    private static function getUptime(): string
    {
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        
        if ($isWindows) {
            $command = 'wmic os get lastbootuptime /value';
            $output = @shell_exec($command);
            // Parse Windows uptime (complex, simplified here)
            return 'N/A';
        } else {
            $uptime = @file_get_contents('/proc/uptime');
            if ($uptime) {
                $seconds = (float) explode(' ', $uptime)[0];
                return self::formatUptime($seconds);
            }
        }
        
        return 'Unknown';
    }

    /**
     * Format uptime
     */
    private static function formatUptime(float $seconds): string
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        $parts = [];
        if ($days > 0) $parts[] = $days . ' day' . ($days > 1 ? 's' : '');
        if ($hours > 0) $parts[] = $hours . ' hour' . ($hours > 1 ? 's' : '');
        if ($minutes > 0) $parts[] = $minutes . ' minute' . ($minutes > 1 ? 's' : '');
        
        return !empty($parts) ? implode(', ', $parts) : 'Less than a minute';
    }

    /**
     * Format bytes to human readable
     */
    private static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Parse memory limit string to bytes
     */
    private static function parseMemoryLimit(string $limit): int
    {
        $limit = trim($limit);
        $last = strtolower($limit[strlen($limit) - 1]);
        $value = (int) $limit;
        
        switch ($last) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }
        
        return $value;
    }
}





