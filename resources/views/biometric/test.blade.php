@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Biometric Device Test</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Biometric Test</li>
    </ol>

    <div class="row">
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-fingerprint me-1"></i>
                    ZKTeco Biometric Device Connection Test
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong><i class="fas fa-info-circle"></i> Instructions:</strong><br>
                        <ul class="mb-0 mt-2">
                            <li>Enter your biometric device IP address and port</li>
                            <li>Default port is usually <strong>4370</strong></li>
                            <li>If your device has a Comm Key (password), enter it in the password field</li>
                            <li>Make sure the device is powered on and connected to the same network</li>
                        </ul>
                    </div>

                    <form id="deviceForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="ip" class="form-label">Device IP Address <span class="text-danger">*</span></label>
                                <input type="text" id="ip" name="ip" value="{{ config('zkteco.ip', '192.168.100.108') }}" 
                                       class="form-control" required placeholder="e.g., 192.168.100.108">
                            </div>
                            <div class="col-md-3">
                                <label for="port" class="form-label">Port <span class="text-danger">*</span></label>
                                <input type="number" id="port" name="port" value="{{ config('zkteco.port', 4370) }}" 
                                       class="form-control" required min="1" max="65535">
                            </div>
                            <div class="col-md-3">
                                <label for="password" class="form-label">Comm Key (Password)</label>
                                <input type="number" id="password" name="password" value="{{ config('zkteco.password', 0) }}" 
                                       class="form-control" placeholder="0 (default)">
                                <small class="form-text text-muted">Usually 0 if not set</small>
                            </div>
                        </div>

                        <div class="d-flex gap-2 flex-wrap mb-3">
                            <button type="button" class="btn btn-primary" onclick="testConnection()">
                                <i class="fas fa-plug"></i> Test Connection
                            </button>
                            <button type="button" class="btn btn-success" onclick="getDeviceInfo()">
                                <i class="fas fa-info-circle"></i> Get Device Info
                            </button>
                            <button type="button" class="btn btn-info" onclick="getAttendance()">
                                <i class="fas fa-calendar-check"></i> Get Attendance
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="getUsers()">
                                <i class="fas fa-users"></i> Get Users
                            </button>
                        </div>
                    </form>

                    <div id="loading" class="text-center" style="display:none; margin-top: 20px;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Connecting to device...</p>
                    </div>

                    <div id="result" style="margin-top: 20px; display:none;"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-question-circle me-1"></i>
                    Quick Help
                </div>
                <div class="card-body">
                    <h6 class="fw-bold">Connection Settings:</h6>
                    <ul class="small">
                        <li><strong>IP Address:</strong> Find this in your device settings (usually under Network/Communication)</li>
                        <li><strong>Port:</strong> Default is 4370 for ZKTeco devices</li>
                        <li><strong>Comm Key:</strong> Check device settings → System → Communication → Comm Key</li>
                    </ul>
                    
                    <h6 class="fw-bold mt-3">Troubleshooting:</h6>
                    <ul class="small">
                        <li>Ensure device is powered on</li>
                        <li>Check network connectivity (ping the IP)</li>
                        <li>Verify firewall isn't blocking port 4370</li>
                        <li>If Comm Key error, check device settings</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showLoading(show) {
        const el = document.getElementById('loading');
        if (el) {
            el.style.display = show ? 'block' : 'none';
        }
    }

    function showResult(success, message, data = null) {
        const resultDiv = document.getElementById('result');
        if (!resultDiv) return;
        
        resultDiv.style.display = 'block';
        
        let alertClass = success ? 'alert-success' : 'alert-danger';
        let icon = success ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-exclamation-circle"></i>';
        
        let content = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">`;
        content += `${icon} <strong>${success ? 'Success' : 'Error'}</strong><br>`;
        content += message;
        content += '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        content += '</div>';
        
        if (data) {
            content += '<div class="mt-3"><h6>Response Data:</h6>';
            content += '<pre class="bg-light p-3 rounded" style="max-height: 400px; overflow-y: auto; font-size: 0.875rem;">';
            content += JSON.stringify(data, null, 2);
            content += '</pre></div>';
        }
        
        resultDiv.innerHTML = content;
    }

    function getFormData() {
        return {
            ip: document.getElementById('ip').value,
            port: parseInt(document.getElementById('port').value),
            password: document.getElementById('password').value ? parseInt(document.getElementById('password').value) : 0
        };
    }

    function getCSRFToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.getAttribute('content') : '';
    }

    async function testConnection() {
        showLoading(true);
        const resultDiv = document.getElementById('result');
        if (resultDiv) {
            resultDiv.style.display = 'none';
        }
        
        try {
            const formData = getFormData();
            const response = await fetch('{{ route("biometric.test-connection") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            
            const result = await response.json();
            showResult(result.success, result.message || (result.success ? 'Connection successful!' : 'Connection failed.'), result.device_info || result);
        } catch (error) {
            showResult(false, 'Error: ' + error.message);
        } finally {
            showLoading(false);
        }
    }

    async function getDeviceInfo() {
        showLoading(true);
        const resultDiv = document.getElementById('result');
        if (resultDiv) {
            resultDiv.style.display = 'none';
        }
        
        try {
            const formData = getFormData();
            const response = await fetch('{{ route("biometric.device-info") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            
            const result = await response.json();
            const message = result.success 
                ? 'Device information retrieved successfully!' 
                : (result.message || 'Failed to get device information.');
            showResult(result.success, message, result);
        } catch (error) {
            showResult(false, 'Error: ' + error.message);
        } finally {
            showLoading(false);
        }
    }

    async function getAttendance() {
        showLoading(true);
        const resultDiv = document.getElementById('result');
        if (resultDiv) {
            resultDiv.style.display = 'none';
        }
        
        try {
            const formData = getFormData();
            const response = await fetch('{{ route("biometric.attendance") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            
            const result = await response.json();
            const message = result.success 
                ? `Retrieved ${result.count || 0} attendance record(s) from device` 
                : (result.message || 'Failed to get attendance records.');
            showResult(result.success, message, result);
        } catch (error) {
            showResult(false, 'Error: ' + error.message);
        } finally {
            showLoading(false);
        }
    }

    async function getUsers() {
        showLoading(true);
        const resultDiv = document.getElementById('result');
        if (resultDiv) {
            resultDiv.style.display = 'none';
        }
        
        try {
            const formData = getFormData();
            const response = await fetch('{{ route("biometric.users") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            
            const result = await response.json();
            const message = result.success 
                ? `Retrieved ${result.count || 0} user(s) from device` 
                : (result.message || 'Failed to get users.');
            showResult(result.success, message, result);
        } catch (error) {
            showResult(false, 'Error: ' + error.message);
        } finally {
            showLoading(false);
        }
    }
</script>
@endsection












