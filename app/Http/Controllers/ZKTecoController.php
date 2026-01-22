<?php

namespace App\Http\Controllers;

use App\Services\ZKTecoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ZKTecoController extends Controller
{
    /**
     * Display the biometric device test page
     */
    public function index()
    {
        return view('biometric.test');
    }

    /**
     * Test connection to biometric device
     */
    public function testConnection(Request $request)
    {
        $request->validate([
            'ip' => 'required|ip',
            'port' => 'required|integer|min:1|max:65535',
            'password' => 'nullable|integer'
        ]);

        $ip = $request->input('ip', config('zkteco.ip', '192.168.100.108'));
        $port = $request->input('port', config('zkteco.port', 4370));
        $password = $request->input('password', config('zkteco.password', 0));

        try {
            $zkteco = new ZKTecoService($ip, $port, $password);
            $result = $zkteco->testConnection();

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('ZKTeco test connection error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get device information
     */
    public function getDeviceInfo(Request $request)
    {
        $request->validate([
            'ip' => 'required|ip',
            'port' => 'required|integer|min:1|max:65535',
            'password' => 'nullable|integer'
        ]);

        $ip = $request->input('ip', config('zkteco.ip', '192.168.100.108'));
        $port = $request->input('port', config('zkteco.port', 4370));
        $password = $request->input('password', config('zkteco.password', 0));

        try {
            $zkteco = new ZKTecoService($ip, $port, $password);
            
            if (!$zkteco->connect()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to connect to device. Please check IP, port, and network connectivity.'
                ], 500);
            }

            $deviceInfo = $zkteco->getDeviceInfo();
            $time = $zkteco->getTime();
            $zkteco->disconnect();

            return response()->json([
                'success' => true,
                'device_info' => $deviceInfo,
                'device_time' => $time
            ]);
        } catch (\Exception $e) {
            Log::error('ZKTeco get device info error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get attendance records from device
     */
    public function getAttendance(Request $request)
    {
        $request->validate([
            'ip' => 'required|ip',
            'port' => 'required|integer|min:1|max:65535',
            'password' => 'nullable|integer'
        ]);

        $ip = $request->input('ip', config('zkteco.ip', '192.168.100.108'));
        $port = $request->input('port', config('zkteco.port', 4370));
        $password = $request->input('password', config('zkteco.password', 0));

        try {
            $zkteco = new ZKTecoService($ip, $port, $password);
            
            if (!$zkteco->connect()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to connect to device. Please check IP, port, and network connectivity.'
                ], 500);
            }

            $attendance = $zkteco->getAttendances();
            $zkteco->disconnect();

            return response()->json([
                'success' => true,
                'attendance' => $attendance,
                'count' => count($attendance)
            ]);
        } catch (\Exception $e) {
            Log::error('ZKTeco get attendance error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get users from device
     */
    public function getUsers(Request $request)
    {
        $request->validate([
            'ip' => 'required|ip',
            'port' => 'required|integer|min:1|max:65535',
            'password' => 'nullable|integer'
        ]);

        $ip = $request->input('ip', config('zkteco.ip', '192.168.100.108'));
        $port = $request->input('port', config('zkteco.port', 4370));
        $password = $request->input('password', config('zkteco.password', 0));

        try {
            $zkteco = new ZKTecoService($ip, $port, $password);
            
            if (!$zkteco->connect()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to connect to device. Please check IP, port, and network connectivity.'
                ], 500);
            }

            $users = $zkteco->getUsers();
            $zkteco->disconnect();

            return response()->json([
                'success' => true,
                'users' => $users,
                'count' => count($users)
            ]);
        } catch (\Exception $e) {
            Log::error('ZKTeco get users error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Register a member to the biometric device
     */
    public function registerMember(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'ip' => 'nullable|ip',
            'port' => 'nullable|integer|min:1|max:65535',
            'password' => 'nullable|integer',
            'enroll_id' => 'nullable|integer|min:1|max:65535'
        ]);

        $member = \App\Models\Member::with(['spouseMember', 'children'])->findOrFail($request->member_id);
        
        Log::info("Registering member to device", [
            'member_id' => $member->id,
            'member_name' => $member->full_name,
            'spouse_member_id' => $member->spouse_member_id,
            'children_count' => $member->children->count()
        ]);
        
        $ip = $request->input('ip', config('zkteco.ip', '192.168.100.108'));
        $port = $request->input('port', config('zkteco.port', 4370));
        $password = $request->input('password', config('zkteco.password', 0));
        
        // Use provided enroll_id or member's existing enroll_id (auto-generated)
        $enrollId = $request->input('enroll_id') ?: $member->biometric_enroll_id;

        // If member doesn't have an enroll_id, generate one automatically
        if (empty($enrollId)) {
            try {
                $enrollId = \App\Models\Member::generateBiometricEnrollId();
                $member->biometric_enroll_id = $enrollId;
                $member->save();
                Log::info("Generated enroll ID for member: {$enrollId}");
            } catch (\Exception $e) {
                Log::error("Failed to generate enroll ID: " . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate enroll ID: ' . $e->getMessage()
                ], 500);
            }
        }

        // Validate enroll_id is within range (10-999 for 2-3 digits)
        $enrollIdInt = (int)$enrollId;
        if ($enrollIdInt < 10 || $enrollIdInt > 999) {
            return response()->json([
                'success' => false,
                'message' => 'Enroll ID must be between 10 and 999 (2-3 digits). Current value: ' . $enrollId
            ], 400);
        }

        try {
            // Connect to device ONCE - keep connection open for all registrations
            $zkteco = new ZKTecoService($ip, $port, $password);
            
            Log::info("=== STARTING FAMILY REGISTRATION ===");
            Log::info("Connecting to device: {$ip}:{$port}");
            
            if (!$zkteco->connect()) {
                Log::error("Failed to connect to device");
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to connect to device. Please check IP, port, and network connectivity.'
                ], 500);
            }

            Log::info("✅ Connected to device successfully");

            $registered = [];
            $errors = [];

            // STEP 1: Register main member to device
            Log::info("=== STEP 1: Registering Main Member ===");
            Log::info("Member: {$member->full_name}, Enroll ID: {$enrollId}");
            
            try {
                $result = $zkteco->registerUser(
                    (int)$enrollId,           // UID
                    (string)$enrollId,        // UserID (must be string)
                    $member->full_name,       // Name
                    '',                       // Password (empty for fingerprint devices)
                    0,                        // Role (0 = user)
                    0                         // Card number
                );

                if ($result) {
                    // Update member with enroll_id
                    $member->biometric_enroll_id = (string)$enrollId;
                    $member->save();
                    $registered[] = "Member: {$member->full_name} (ID: {$enrollId})";
                    Log::info("✅ Main member registered successfully");
                } else {
                    // Check if member already exists on device
                    try {
                        $users = $zkteco->getUsers();
                        $memberExists = false;
                        foreach ($users as $user) {
                            $userName = $user['name'] ?? '';
                            if (strtolower(trim($userName)) === strtolower(trim($member->full_name))) {
                                $memberExists = true;
                                $existingUid = $user['uid'] ?? $user['userid'] ?? $user['id'] ?? 'unknown';
                                Log::info("Main member already exists on device with UID: {$existingUid}");
                                $registered[] = "Member: {$member->full_name} (ID: {$existingUid}) - Already on device";
                                break;
                            }
                        }
                        
                        if ($memberExists) {
                            Log::info("✅ Main member already on device - continuing with spouse/children");
                        } else {
                            $errors[] = "Failed to register main member: {$member->full_name} (device returned false)";
                            Log::error("❌ Main member registration failed - not found on device");
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Failed to register main member: {$member->full_name} - " . $e->getMessage();
                        Log::error("❌ Main member registration failed: " . $e->getMessage());
                    }
                }
            } catch (\Exception $e) {
                // Handle exception (e.g., user already exists)
                $errorMsg = $e->getMessage();
                Log::warning("Main member registration exception: {$errorMsg}");
                
                if (strpos($errorMsg, 'already exists') !== false) {
                    // User already exists - check device to find their ID
                    try {
                        $users = $zkteco->getUsers();
                        foreach ($users as $user) {
                            $userName = $user['name'] ?? '';
                            if (strtolower(trim($userName)) === strtolower(trim($member->full_name))) {
                                $existingUid = $user['uid'] ?? $user['userid'] ?? $user['id'] ?? 'unknown';
                                $registered[] = "Member: {$member->full_name} (ID: {$existingUid}) - Already on device";
                                Log::info("✅ Main member already on device with UID: {$existingUid}");
                                break;
                            }
                        }
                    } catch (\Exception $checkE) {
                        $registered[] = "Member: {$member->full_name} (ID: {$enrollId}) - Already on device";
                        Log::info("✅ Main member already on device (could not verify ID)");
                    }
                } else {
                    $errors[] = "Failed to register main member: {$member->full_name} - {$errorMsg}";
                    Log::error("❌ Main member registration exception: {$errorMsg}");
                }
                // Continue with spouse and children registration anyway
            }
                
            // STEP 2: Register spouse if they are a church member
            Log::info("=== STEP 2: Checking for Spouse ===");
            Log::info("Spouse member ID: " . ($member->spouse_member_id ?? 'NULL'));
            
            if ($member->spouse_member_id) {
                // Reload spouse relationship to ensure we have latest data
                $member->load('spouseMember');
                $spouse = $member->spouseMember;
                
                if ($spouse) {
                    Log::info("✅ Spouse found: {$spouse->full_name} (ID: {$spouse->id})");
                    
                    // Generate enroll ID if needed
                    if (!$spouse->biometric_enroll_id) {
                        Log::info("Generating enroll ID for spouse: {$spouse->full_name}");
                        $spouseEnrollId = \App\Models\Member::generateBiometricEnrollId();
                        $spouse->biometric_enroll_id = $spouseEnrollId;
                        $spouse->save();
                        Log::info("Generated enroll ID: {$spouseEnrollId}");
                    } else {
                        $spouseEnrollId = $spouse->biometric_enroll_id;
                        Log::info("Spouse already has enroll ID: {$spouseEnrollId}");
                    }
                    
                    // Register spouse to device (connection is still open)
                    Log::info("Registering spouse to device: {$spouse->full_name} (ID: {$spouseEnrollId})");
                    
                    // Verify connection is still active before registering
                    if (!$zkteco->connect()) {
                        Log::error("Connection lost! Reconnecting...");
                        if (!$zkteco->connect()) {
                            $errors[] = "Failed to maintain connection to device while registering spouse";
                            Log::error("❌ Could not reconnect to device");
                        } else {
                            Log::info("✅ Reconnected successfully");
                        }
                    }
                    
                    // Small delay to ensure device is ready
                    usleep(500000); // 0.5 second delay
                    
                    try {
                        $spouseResult = $zkteco->registerUser(
                            (int)$spouseEnrollId,
                            (string)$spouseEnrollId,
                            $spouse->full_name,
                            '',
                            0,
                            0
                        );
                        
                        if ($spouseResult) {
                            $registered[] = "Spouse: {$spouse->full_name} (ID: {$spouseEnrollId})";
                            Log::info("✅ Spouse registered successfully: {$spouse->full_name}");
                        } else {
                            $errors[] = "Failed to register spouse: {$spouse->full_name} (device returned false)";
                            Log::error("❌ Spouse registration failed - device returned false");
                        }
                    } catch (\Exception $e) {
                        $errorMsg = "Failed to register spouse {$spouse->full_name}: " . $e->getMessage();
                        Log::error("❌ Spouse registration exception: " . $e->getMessage(), ['exception' => $e]);
                        
                        // If user already exists, that's OK - they're already registered
                        if (strpos($e->getMessage(), 'already exists') !== false) {
                            $registered[] = "Spouse: {$spouse->full_name} (ID: {$spouseEnrollId}) - Already on device";
                            Log::info("✅ Spouse already on device: {$spouse->full_name}");
                        } else {
                            // Check if spouse exists on device with different ID
                            try {
                                $users = $zkteco->getUsers();
                                foreach ($users as $user) {
                                    $userName = $user['name'] ?? '';
                                    if (strtolower(trim($userName)) === strtolower(trim($spouse->full_name))) {
                                        $existingUid = $user['uid'] ?? $user['userid'] ?? $user['id'] ?? 'unknown';
                                        $registered[] = "Spouse: {$spouse->full_name} (ID: {$existingUid}) - Already on device";
                                        Log::info("✅ Spouse already on device with UID: {$existingUid}");
                                        break;
                                    }
                                }
                            } catch (\Exception $checkE) {
                                $errors[] = $errorMsg;
                            }
                        }
                    }
                } else {
                    $errors[] = "Spouse member ID {$member->spouse_member_id} not found in database";
                    Log::warning("❌ Spouse member ID {$member->spouse_member_id} not found");
                }
            } else {
                Log::info("No spouse_member_id - spouse may not be a church member");
                if ($member->spouse_full_name) {
                    Log::info("Member has spouse info but spouse is not a church member: {$member->spouse_full_name}");
                }
            }
                
            // STEP 3: Register teenager children (13-17) who should attend main service
            Log::info("=== STEP 3: Checking for Teenager Children ===");
            
            // Reload children relationship to ensure we have latest data
            $member->load('children');
            $allChildren = $member->children()->whereNotNull('date_of_birth')->get();
            Log::info("Found {$allChildren->count()} children with date of birth");
            
            $teenagers = $allChildren->filter(function($child) {
                $isTeenager = $child->shouldAttendMainService();
                Log::info("Child {$child->full_name} (age: {$child->getAge()}) - is teenager: " . ($isTeenager ? 'YES ✅' : 'NO ❌'));
                return $isTeenager; // Only teenagers (13-17)
            });
            
            Log::info("Found {$teenagers->count()} teenagers to register");
            
            foreach ($teenagers as $index => $teenager) {
                Log::info("Processing teenager " . ($index + 1) . ": {$teenager->full_name} (age: {$teenager->getAge()})");
                
                // Generate enroll ID if needed
                if (!$teenager->biometric_enroll_id) {
                    Log::info("Generating enroll ID for teenager: {$teenager->full_name}");
                    $teenEnrollId = \App\Models\Child::generateBiometricEnrollId();
                    $teenager->biometric_enroll_id = $teenEnrollId;
                    $teenager->save();
                    Log::info("Generated enroll ID: {$teenEnrollId}");
                } else {
                    $teenEnrollId = $teenager->biometric_enroll_id;
                    Log::info("Teenager already has enroll ID: {$teenEnrollId}");
                }
                
                // Register teenager to device (connection is still open)
                Log::info("Registering teenager to device: {$teenager->full_name} (ID: {$teenEnrollId})");
                
                // Verify connection is still active before registering
                if (!$zkteco->connect()) {
                    Log::error("Connection lost! Reconnecting...");
                    if (!$zkteco->connect()) {
                        $errors[] = "Failed to maintain connection to device while registering teenager";
                        Log::error("❌ Could not reconnect to device");
                        continue; // Skip this teenager and try next
                    } else {
                        Log::info("✅ Reconnected successfully");
                    }
                }
                
                // Small delay to ensure device is ready
                usleep(500000); // 0.5 second delay
                
                try {
                    $teenResult = $zkteco->registerUser(
                        (int)$teenEnrollId,
                        (string)$teenEnrollId,
                        $teenager->full_name,
                        '',
                        0,
                        0
                    );
                    
                    if ($teenResult) {
                        $registered[] = "Teenager: {$teenager->full_name} (ID: {$teenEnrollId})";
                        Log::info("✅ Teenager registered successfully: {$teenager->full_name}");
                    } else {
                        $errors[] = "Failed to register teenager: {$teenager->full_name} (device returned false)";
                        Log::error("❌ Teenager registration failed - device returned false");
                    }
                } catch (\Exception $e) {
                    $errorMsg = "Failed to register teenager {$teenager->full_name}: " . $e->getMessage();
                    Log::error("❌ Teenager registration exception: " . $e->getMessage(), ['exception' => $e]);
                    
                    // If user already exists, that's OK - they're already registered
                    if (strpos($e->getMessage(), 'already exists') !== false) {
                        $registered[] = "Teenager: {$teenager->full_name} (ID: {$teenEnrollId}) - Already on device";
                        Log::info("✅ Teenager already on device: {$teenager->full_name}");
                    } else {
                        // Check if teenager exists on device with different ID
                        try {
                            $users = $zkteco->getUsers();
                            foreach ($users as $user) {
                                $userName = $user['name'] ?? '';
                                if (strtolower(trim($userName)) === strtolower(trim($teenager->full_name))) {
                                    $existingUid = $user['uid'] ?? $user['userid'] ?? $user['id'] ?? 'unknown';
                                    $registered[] = "Teenager: {$teenager->full_name} (ID: {$existingUid}) - Already on device";
                                    Log::info("✅ Teenager already on device with UID: {$existingUid}");
                                    break;
                                }
                            }
                        } catch (\Exception $checkE) {
                            $errors[] = $errorMsg;
                        }
                    }
                }
            }

            // Disconnect from device (all registrations complete)
            Log::info("=== REGISTRATION COMPLETE ===");
            Log::info("Total registered: " . count($registered));
            Log::info("Total errors: " . count($errors));
            $zkteco->disconnect();

            $message = "Successfully registered to biometric device:\n" . implode("\n", $registered);
            
            if (!empty($errors)) {
                $message .= "\n\n⚠️ Errors/Warnings:\n" . implode("\n", $errors);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'enroll_id' => $enrollId,
                'device_registered' => true,
                'registered_count' => count($registered),
                'errors' => $errors,
                'registered' => $registered
            ]);
        } catch (\Exception $e) {
            Log::error('ZKTeco register member error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Register multiple members to device (bulk registration)
     */
    public function registerMembersBulk(Request $request)
    {
        $request->validate([
            'member_ids' => 'required|array',
            'member_ids.*' => 'exists:members,id',
            'ip' => 'nullable|ip',
            'port' => 'nullable|integer|min:1|max:65535',
            'password' => 'nullable|integer'
        ]);

        $memberIds = $request->member_ids;
        $ip = $request->input('ip', config('zkteco.ip', '192.168.100.108'));
        $port = $request->input('port', config('zkteco.port', 4370));
        $password = $request->input('password', config('zkteco.password', 0));

        $results = [
            'success' => [],
            'failed' => [],
            'skipped' => []
        ];

        try {
            $zkteco = new ZKTecoService($ip, $port, $password);
            
            if (!$zkteco->connect()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to connect to device. Please check IP, port, and network connectivity.'
                ], 500);
            }

            foreach ($memberIds as $memberId) {
                try {
                    $member = \App\Models\Member::find($memberId);
                    if (!$member) {
                        $results['skipped'][] = [
                            'member_id' => $memberId,
                            'reason' => 'Member not found'
                        ];
                        continue;
                    }

                    // Use existing enroll_id or generate one automatically
                    $enrollId = $member->biometric_enroll_id;
                    
                    // If member doesn't have an enroll_id, generate one automatically
                    if (empty($enrollId)) {
                        try {
                            $enrollId = \App\Models\Member::generateBiometricEnrollId();
                            $member->biometric_enroll_id = $enrollId;
                            $member->save();
                        } catch (\Exception $e) {
                            $results['skipped'][] = [
                                'member_id' => $memberId,
                                'member_name' => $member->full_name,
                                'reason' => 'Failed to generate enroll ID: ' . $e->getMessage()
                            ];
                            continue;
                        }
                    }

                    // Validate enroll_id is within range (10-999 for 2-3 digits)
                    $enrollIdInt = (int)$enrollId;
                    if ($enrollIdInt < 10 || $enrollIdInt > 999) {
                        $results['skipped'][] = [
                            'member_id' => $memberId,
                            'member_name' => $member->full_name,
                            'reason' => "Enroll ID {$enrollId} is out of range (10-999)"
                        ];
                        continue;
                    }

                    // Register to device
                    $result = $zkteco->registerUser(
                        (int)$enrollId,
                        (string)$enrollId,
                        $member->full_name,
                        '',
                        0,
                        0
                    );

                    if ($result) {
                        $member->biometric_enroll_id = (string)$enrollId;
                        $member->save();

                        $results['success'][] = [
                            'member_id' => $memberId,
                            'member_name' => $member->full_name,
                            'enroll_id' => $enrollId
                        ];
                    } else {
                        $results['failed'][] = [
                            'member_id' => $memberId,
                            'member_name' => $member->full_name,
                            'reason' => 'Device registration returned false'
                        ];
                    }

                    // Small delay between registrations
                    usleep(500000); // 0.5 seconds

                } catch (\Exception $e) {
                    $results['failed'][] = [
                        'member_id' => $memberId,
                        'member_name' => $member->full_name ?? 'Unknown',
                        'reason' => $e->getMessage()
                    ];
                }
            }

            $zkteco->disconnect();

            $total = count($memberIds);
            $successCount = count($results['success']);
            $failedCount = count($results['failed']);
            $skippedCount = count($results['skipped']);

            return response()->json([
                'success' => true,
                'message' => "Bulk registration completed: {$successCount} succeeded, {$failedCount} failed, {$skippedCount} skipped out of {$total} members.",
                'results' => $results,
                'summary' => [
                    'total' => $total,
                    'success' => $successCount,
                    'failed' => $failedCount,
                    'skipped' => $skippedCount
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('ZKTeco bulk register members error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search members by name for autocomplete
     */
    public function searchMembers(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:1'
        ]);

        $query = trim($request->input('q'));
        
        if (empty($query)) {
            return response()->json([
                'success' => true,
                'members' => []
            ]);
        }
        
        // Normalize query: trim, lowercase for case-insensitive search
        $normalizedQuery = strtolower($query);
        
        // Search with case-insensitive matching
        $members = \App\Models\Member::whereRaw('LOWER(full_name) LIKE ?', ["%{$normalizedQuery}%"])
            ->select('id', 'full_name', 'member_id', 'biometric_enroll_id', 'phone_number')
            ->orderBy('full_name')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'members' => $members->map(function($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->full_name,
                    'member_id' => $member->member_id,
                    'enroll_id' => $member->biometric_enroll_id,
                    'phone' => $member->phone_number,
                    'display' => $member->full_name . ' (' . $member->member_id . ')'
                ];
            })
        ]);
    }
}

