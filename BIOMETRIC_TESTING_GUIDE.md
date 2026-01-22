# Biometric Device Testing Guide

## Quick Start - How to Test Your Biometric Device

### Step 1: Access the Test Page

1. **Login to your Waumini system** (if not already logged in)
2. **Navigate to the test page** using one of these methods:
   - Direct URL: `http://your-domain/biometric/test`
   - Or add a link in your dashboard/menu

### Step 2: Gather Device Information

Before testing, you need to know:
- **Device IP Address**: The IP address of your ZKTeco biometric device
  - Find this in: Device Settings → Network/Communication → IP Address
  - Common examples: `192.168.100.108`, `192.168.1.100`
- **Port**: Usually `4370` (default for ZKTeco devices)
- **Comm Key (Password)**: 
  - Find this in: Device Settings → System → Communication → Comm Key
  - If not set, use `0` (default)

### Step 3: Test Connection

1. **Enter Device Details**:
   - IP Address: Enter your device IP (e.g., `192.168.100.108`)
   - Port: Enter port number (usually `4370`)
   - Comm Key: Enter password if set, or leave as `0`

2. **Click "Test Connection"**:
   - This will attempt to connect to your device
   - Wait for the result (may take 10-30 seconds)
   - ✅ **Success**: You'll see device information
   - ❌ **Error**: Check the error message for troubleshooting

### Step 4: Test Other Functions

Once connection is successful, try:

1. **Get Device Info**: 
   - Retrieves device name, serial number, firmware version
   - Shows device time and other details

2. **Get Attendance**:
   - Downloads all attendance records from the device
   - Shows count and list of attendance entries

3. **Get Users**:
   - Lists all users registered on the device
   - Shows user IDs, names, and enrollment details

## Troubleshooting

### Connection Failed

**Possible Issues:**
- ❌ Device is not powered on
- ❌ Wrong IP address
- ❌ Device not on same network
- ❌ Firewall blocking port 4370
- ❌ Wrong Comm Key (password)

**Solutions:**
1. **Ping the device**:
   ```bash
   ping 192.168.100.108
   ```
   If ping fails, check network connectivity

2. **Check IP address**:
   - Verify IP on device: Settings → Network
   - Ensure it's on the same network as your server

3. **Check Comm Key**:
   - Go to device: Settings → System → Communication → Comm Key
   - Update `.env` file: `ZKTECO_PASSWORD=your_comm_key`
   - Or enter it in the test form

4. **Check Firewall**:
   - Ensure port 4370 is open
   - Windows Firewall may block the connection

### Authentication Error (Comm Key)

**Error Message**: "CMD_ACK_UNAUTH (2005)" or "authentication required"

**Solution**:
1. Check device Comm Key: Settings → System → Communication
2. Update `.env`:
   ```
   ZKTECO_PASSWORD=12345
   ```
   (Replace 12345 with your actual Comm Key)
3. If Comm Key is 0, set: `ZKTECO_PASSWORD=0`

### Timeout Error

**Error Message**: "Connection timeout" or "timed out"

**Solutions**:
1. Check if device is powered on
2. Verify network connection
3. Try increasing timeout in `ZKTecoService.php` (currently 30 seconds)
4. Check if device is busy with another operation

## Testing Checklist

- [ ] Device is powered on
- [ ] Device is connected to network
- [ ] IP address is correct
- [ ] Port is correct (usually 4370)
- [ ] Comm Key is correct (or 0 if not set)
- [ ] Server can ping device IP
- [ ] Firewall allows port 4370
- [ ] Test connection works
- [ ] Get device info works
- [ ] Get attendance works
- [ ] Get users works

## Example Test Scenarios

### Scenario 1: First Time Setup
1. Find device IP address (check device display or settings)
2. Enter IP in test form
3. Leave port as 4370
4. Leave Comm Key as 0
5. Click "Test Connection"
6. If fails, check Comm Key on device

### Scenario 2: Device with Comm Key
1. Check device Comm Key: Settings → System → Communication
2. Enter IP, port, and Comm Key in form
3. Click "Test Connection"
4. Should connect successfully

### Scenario 3: Network Issues
1. Ping device IP from server
2. If ping fails, check network cables/routers
3. Verify device and server are on same network
4. Check firewall settings

## Next Steps After Successful Test

Once connection is working:
1. ✅ You can sync attendance from device
2. ✅ You can register members to device
3. ✅ You can retrieve attendance records
4. ✅ Integration is ready for production use

## Need Help?

- Check device manual for network settings
- Verify device firmware is up to date
- Contact ZKTeco support if device-specific issues
- Check Laravel logs: `storage/logs/laravel.log`












