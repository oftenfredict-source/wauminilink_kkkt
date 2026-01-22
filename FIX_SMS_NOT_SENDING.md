# Fix SMS Not Sending for New Members and OTP

## Problem
- SMS not sent when registering new members
- SMS not sent when requesting OTP during login

## Root Causes

### 1. SMS Notifications Disabled
The system checks if `enable_sms_notifications` is enabled before sending any SMS.

### 2. Missing SMS Configuration
SMS requires either:
- Username + Password (for messaging-service.co.tz)
- OR API URL + API Key (for other providers)

### 3. Missing Phone Numbers
- Member has no phone number in database
- User account has no phone number
- Phone number format is incorrect

## Quick Fix

### Step 1: Enable SMS Notifications

Run this command:
```bash
php enable_sms.php
```

This will:
- Enable SMS notifications
- Set default SMS credentials (username/password)
- Configure sender ID

### Step 2: Verify SMS is Enabled

Check the logs after registering a member or requesting OTP. Look for:
- `Welcome SMS sent successfully` ✅
- `Login OTP sent successfully` ✅
- `SMS notifications disabled` ❌
- `SMS config missing` ❌

### Step 3: Check Phone Numbers

Make sure:
1. **New members** have a phone number when registering
2. **Users** have a phone number OR their associated member has a phone number

## Detailed Troubleshooting

### Check SMS Status

Run the diagnostic script:
```bash
php check_sms_status.php
```

Or check manually:
1. Go to System Settings in admin panel
2. Look for "Enable SMS Notifications" - should be ON
3. Check SMS configuration:
   - SMS API URL
   - SMS Username
   - SMS Password
   - SMS Sender ID

### Check Logs

Check `storage/logs/laravel.log` for SMS-related entries:

**For Member Registration:**
- Search for: `Welcome SMS`
- Look for: `ok => true` (success) or error messages

**For OTP:**
- Search for: `Login OTP`
- Look for: `ok => true` (success) or error messages

### Common Issues

#### Issue 1: "SMS notifications disabled"
**Solution:** Enable in System Settings or run `php enable_sms.php`

#### Issue 2: "SMS config missing"
**Solution:** Configure SMS credentials in System Settings:
- Set SMS Username
- Set SMS Password
- OR set SMS API URL + API Key

#### Issue 3: "No phone number"
**Solution:** 
- For members: Add phone number when registering
- For users: Add phone number to user account or ensure member has phone number

#### Issue 4: SMS sent but not received
**Possible causes:**
- Wrong phone number format (should be +255XXXXXXXXX)
- SMS provider issue
- Phone number doesn't support SMS

## Manual Configuration

If `enable_sms.php` doesn't work, configure manually:

### Via System Settings (Admin Panel)
1. Login as admin
2. Go to Settings → System Settings
3. Find "Notifications" section
4. Enable "Enable SMS Notifications"
5. Set:
   - SMS API URL: `https://messaging-service.co.tz/link/sms/v1/text/single`
   - SMS Username: `your_username`
   - SMS Password: `your_password`
   - SMS Sender ID: `WauminiLnk`

### Via Database
```sql
UPDATE system_settings 
SET value = '1' 
WHERE key = 'enable_sms_notifications';

UPDATE system_settings 
SET value = 'https://messaging-service.co.tz/link/sms/v1/text/single' 
WHERE key = 'sms_api_url';

UPDATE system_settings 
SET value = 'your_username' 
WHERE key = 'sms_username';

UPDATE system_settings 
SET value = 'your_password' 
WHERE key = 'sms_password';

UPDATE system_settings 
SET value = 'WauminiLnk' 
WHERE key = 'sms_sender_id';
```

## Test SMS

After enabling, test SMS:

```bash
php test_sms_simple.php
```

Or use the artisan command:
```bash
php artisan sms:test
```

## What Was Fixed

1. ✅ Improved logging for member registration SMS
2. ✅ Better error messages showing why SMS failed
3. ✅ Added diagnostic scripts to check SMS status
4. ✅ OTP SMS already had good logging

## Next Steps

1. Run `php enable_sms.php` to enable SMS
2. Register a new member and check logs
3. Request OTP and check logs
4. If still not working, check the logs for specific error messages

## Log Locations

- Application logs: `storage/logs/laravel.log`
- Search for: `Welcome SMS`, `Login OTP`, `SMS`

## Support

If SMS still doesn't work after following these steps:
1. Check `storage/logs/laravel.log` for error messages
2. Verify SMS provider credentials are correct
3. Test SMS directly with provider API
4. Check if phone numbers are in correct format (+255XXXXXXXXX)

















