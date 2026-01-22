# Troubleshooting Leader Appointment SMS Not Sending

## Problem
SMS is not being sent to members when they are assigned to leadership positions.

## Quick Checks

### 1. Check SMS Notifications are Enabled

Run this command to check:
```bash
php artisan tinker
```

Then run:
```php
\App\Services\SettingsService::get('enable_sms_notifications', false)
```

If it returns `false`, enable it:
```php
\App\Services\SettingsService::set('enable_sms_notifications', true, 'boolean');
```

Or use the enable script:
```bash
php enable_sms.php
```

### 2. Check Member Has Phone Number

When assigning a leadership position, make sure the member has a phone number in their profile.

Check in database:
```sql
SELECT id, full_name, phone_number FROM members WHERE id = [MEMBER_ID];
```

Or check via tinker:
```php
$member = \App\Models\Member::find([MEMBER_ID]);
echo $member->phone_number;
```

### 3. Check SMS Configuration

Verify SMS settings are configured:
```php
\App\Services\SettingsService::get('sms_api_url');
\App\Services\SettingsService::get('sms_username');
\App\Services\SettingsService::get('sms_password');
\App\Services\SettingsService::get('sms_sender_id');
```

### 4. Check Logs

Check `storage/logs/laravel.log` for SMS-related entries:

**Search for:**
- `Leader appointment SMS attempt`
- `Leader appointment SMS sent successfully`
- `Leader appointment SMS failed`
- `SMS notifications disabled`
- `Member has no phone number`

**Look for entries like:**
```
[timestamp] local.INFO: Leader appointment SMS attempt {"leader_id":1,"leader_name":"John Doe",...}
[timestamp] local.INFO: Leader appointment SMS sent successfully {"leader_id":1,...}
```

Or errors:
```
[timestamp] local.WARNING: Leader appointment SMS failed {"leader_id":1,"error":"..."}
[timestamp] local.INFO: SMS notifications disabled, skipping leader appointment notification
```

## Common Issues and Solutions

### Issue 1: "SMS notifications disabled"

**Solution:**
1. Go to System Settings in admin panel
2. Enable "Enable SMS Notifications"
3. Or run: `php enable_sms.php`

### Issue 2: "Member has no phone number"

**Solution:**
1. Edit the member profile
2. Add/update phone number
3. Re-assign the leadership position (or update the existing leader record)

### Issue 3: SMS Configuration Missing

**Solution:**
1. Go to System Settings
2. Configure SMS settings:
   - SMS API URL
   - SMS Username
   - SMS Password
   - SMS Sender ID

Or run the enable script:
```bash
php enable_sms.php
```

### Issue 4: SMS Service Error

**Check the logs for specific error messages:**
- API connection errors
- Authentication failures
- Invalid phone number format

**Solution:**
1. Check SMS provider credentials
2. Verify phone number format (should be in E.164 format: +255XXXXXXXXX)
3. Test SMS service directly:
```php
$smsService = app(\App\Services\SmsService::class);
$result = $smsService->sendDebug('+255743001243', 'Test message');
print_r($result);
```

## Testing SMS Sending

### Test 1: Check if SMS is enabled
```bash
php artisan tinker
```
```php
\App\Services\SettingsService::get('enable_sms_notifications', false)
```

### Test 2: Test SMS service directly
```php
$smsService = app(\App\Services\SmsService::class);
$result = $smsService->sendDebug('+255743001243', 'Test message from system');
print_r($result);
```

### Test 3: Check member phone number
```php
$member = \App\Models\Member::find([MEMBER_ID]);
echo "Phone: " . $member->phone_number . "\n";
echo "Has phone: " . (!empty($member->phone_number) ? 'Yes' : 'No') . "\n";
```

### Test 4: Simulate leader appointment SMS
```php
$leader = \App\Models\Leader::with('member')->first();
$controller = new \App\Http\Controllers\LeaderController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('sendLeaderAppointmentSms');
$method->setAccessible(true);
$result = $method->invoke($controller, $leader, null);
print_r($result);
```

## What Happens When Leader is Assigned

1. **Leader record is created** in database
2. **Member relationship is loaded**
3. **User account is created/updated** (if position requires it)
4. **SMS is attempted** with the following checks:
   - SMS notifications enabled? ✓
   - Member has phone number? ✓
   - SMS service configured? ✓
5. **Result is logged** in `storage/logs/laravel.log`

## Success Indicators

After assigning a leadership position, you should see:

1. **In the success message:**
   - "Login credentials sent via SMS" (if SMS sent successfully)
   - Or a note about why SMS wasn't sent

2. **In the logs:**
   ```
   [timestamp] local.INFO: Leader appointment SMS sent successfully
   ```

3. **Member receives SMS** with:
   - Appointment notification
   - Login credentials (if user account was created)
   - Instructions to change password

## Debugging Steps

1. **Check if SMS is enabled:**
   ```bash
   php artisan tinker
   ```
   ```php
   \App\Services\SettingsService::get('enable_sms_notifications')
   ```

2. **Check member phone number:**
   ```php
   $leader = \App\Models\Leader::with('member')->find([LEADER_ID]);
   echo $leader->member->phone_number;
   ```

3. **Check recent logs:**
   ```bash
   tail -n 100 storage/logs/laravel.log | grep "Leader appointment SMS"
   ```

4. **Test SMS service:**
   ```php
   $smsService = app(\App\Services\SmsService::class);
   $result = $smsService->sendDebug('+255743001243', 'Test');
   print_r($result);
   ```

## Still Not Working?

1. **Check all logs** in `storage/logs/laravel.log`
2. **Verify SMS provider** is working (test with a simple script)
3. **Check phone number format** (should be +255XXXXXXXXX)
4. **Verify member exists** and has phone number
5. **Check SMS quota/balance** with your SMS provider

## Related Files

- `app/Http/Controllers/LeaderController.php` - Leader assignment and SMS sending
- `app/Services/SmsService.php` - SMS service implementation
- `app/Services/SettingsService.php` - Settings management
- `storage/logs/laravel.log` - Application logs

