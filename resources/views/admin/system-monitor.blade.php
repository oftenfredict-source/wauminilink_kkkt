@extends('layouts.index')

@section('content')
<style>
    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 10px !important;
            padding-right: 10px !important;
        }

        .dashboard-header {
            margin-bottom: 12px !important;
            border-radius: 12px !important;
            overflow: hidden !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
        }

        .dashboard-header .card-body {
            padding: 12px 14px !important;
        }

        .dashboard-header .rounded-circle {
            width: 38px !important;
            height: 38px !important;
            min-width: 38px !important;
            flex-shrink: 0 !important;
            background: rgba(255,255,255,0.2) !important;
            border: 2px solid rgba(255,255,255,0.3) !important;
        }

        .dashboard-header .rounded-circle i {
            font-size: 0.95rem !important;
        }

        .dashboard-header .d-flex.align-items-center.gap-3 {
            gap: 12px !important;
            flex: 1 !important;
            min-width: 0 !important;
        }

        .dashboard-header .lh-sm {
            flex: 1 !important;
            min-width: 0 !important;
            overflow: hidden !important;
        }

        .dashboard-header h5 {
            font-size: 1rem !important;
            line-height: 1.3 !important;
            margin-bottom: 2px !important;
            font-weight: 600 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .dashboard-header small {
            font-size: 0.75rem !important;
            line-height: 1.2 !important;
            display: block !important;
            opacity: 0.9 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .dashboard-header .btn {
            margin-top: 0 !important;
            padding: 8px 14px !important;
            font-size: 0.85rem !important;
            border-radius: 8px !important;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
            font-weight: 500 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
            transition: all 0.2s ease !important;
        }

        .dashboard-header .btn:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 6px rgba(0,0,0,0.15) !important;
        }

        .dashboard-header .d-flex.justify-content-between {
            align-items: center !important;
            flex-wrap: nowrap !important;
        }

        .dashboard-header .d-flex.justify-content-between > div:first-child {
            flex: 1 !important;
            min-width: 0 !important;
            overflow: hidden !important;
        }

        /* Cache buttons - stack on mobile */
        .row.g-3 .col-md-3 {
            width: 100%;
            margin-bottom: 10px;
        }

        .card-body {
            padding: 15px !important;
        }

        .card-header {
            padding: 10px 15px !important;
        }
    }

    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 5px !important;
            padding-right: 5px !important;
        }

        .dashboard-header {
            margin-bottom: 10px !important;
            border-radius: 10px !important;
        }

        .dashboard-header .card-body {
            padding: 10px 12px !important;
        }

        .dashboard-header .rounded-circle {
            width: 36px !important;
            height: 36px !important;
            min-width: 36px !important;
        }

        .dashboard-header .rounded-circle i {
            font-size: 0.9rem !important;
        }

        .dashboard-header .d-flex.align-items-center.gap-3 {
            gap: 10px !important;
        }

        .dashboard-header h5 {
            font-size: 0.95rem !important;
            line-height: 1.25 !important;
            margin-bottom: 1px !important;
        }

        .dashboard-header small {
            font-size: 0.72rem !important;
            line-height: 1.15 !important;
        }

        .dashboard-header .d-flex.justify-content-between {
            flex-wrap: wrap !important;
            gap: 8px !important;
        }

        .dashboard-header .btn {
            margin-top: 0 !important;
            width: auto !important;
            min-width: fit-content !important;
            padding: 7px 12px !important;
            font-size: 0.8rem !important;
            flex: 0 0 auto !important;
        }

        /* Stack on very small screens */
        @media (max-width: 400px) {
            .dashboard-header .d-flex.justify-content-between {
                flex-direction: column !important;
                align-items: stretch !important;
            }

            .dashboard-header .btn {
                width: 100% !important;
                margin-top: 8px !important;
            }
        }

        .btn {
            width: 100%;
            margin-bottom: 10px;
        }
    }
</style>

<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm dashboard-header" style="background:#17082d;">
                <div class="card-body text-white py-2 px-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center border border-white border-2" style="width:48px; height:48px; background:rgba(255,255,255,.15);">
                                <i class="fas fa-server text-white"></i>
                            </div>
                            <div class="lh-sm">
                                <h5 class="mb-0 fw-semibold" style="color: white !important;">System Monitor</h5>
                                <small style="color: white !important;">Server monitoring and cache management</small>
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-light btn-sm" onclick="refreshSystemInfo()">
                                <i class="fas fa-sync-alt me-1"></i>Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Cache Management -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-broom me-2"></i>Cache Management
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Clear various types of cache to free up memory and ensure fresh data.</p>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <button class="btn btn-outline-danger w-100" onclick="clearCache('all')">
                                <i class="fas fa-trash-alt me-2"></i>Clear All Cache
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-outline-warning w-100" onclick="clearCache('application')">
                                <i class="fas fa-database me-2"></i>Application Cache
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-outline-info w-100" onclick="clearCache('config')">
                                <i class="fas fa-cog me-2"></i>Config Cache
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-outline-secondary w-100" onclick="clearCache('view')">
                                <i class="fas fa-eye me-2"></i>View Cache
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-outline-primary w-100" onclick="clearCache('route')">
                                <i class="fas fa-route me-2"></i>Route Cache
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-outline-success w-100" onclick="clearCache('optimize')">
                                <i class="fas fa-tachometer-alt me-2"></i>Optimize Cache
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-outline-dark w-100" onclick="clearCache('laravel')">
                                <i class="fas fa-layer-group me-2"></i>Laravel Cache
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="row">
        <!-- CPU Information -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-microchip me-2"></i>CPU Information
                    </h6>
                </div>
                <div class="card-body">
                    <div id="cpuInfo">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading CPU information...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Memory Information -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-memory me-2"></i>Memory Information
                    </h6>
                </div>
                <div class="card-body">
                    <div id="memoryInfo">
                        <div class="text-center py-4">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading memory information...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Storage Information -->
        <div class="col-md-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-hdd me-2"></i>Storage Information
                    </h6>
                </div>
                <div class="card-body">
                    <div id="storageInfo">
                        <div class="text-center py-4">
                            <div class="spinner-border text-warning" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading storage information...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Server Information -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-server me-2"></i>Server Information
                    </h6>
                </div>
                <div class="card-body">
                    <div id="serverInfo">
                        <div class="text-center py-4">
                            <div class="spinner-border text-dark" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading server information...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PHP Information -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">
                        <i class="fab fa-php me-2"></i>PHP Information
                    </h6>
                </div>
                <div class="card-body">
                    <div id="phpInfo">
                        <div class="text-center py-4">
                            <div class="spinner-border text-secondary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading PHP information...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let refreshInterval;

// Load system info on page load
document.addEventListener('DOMContentLoaded', function() {
    loadSystemInfo();
    
    // Auto-refresh every 30 seconds
    refreshInterval = setInterval(loadSystemInfo, 30000);
});

function loadSystemInfo() {
    fetch('{{ route("admin.system-info") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displaySystemInfo(data.data);
            } else {
                showError('Failed to load system information');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Error loading system information');
        });
}

function displaySystemInfo(info) {
    // CPU Info
    const cpuHtml = `
        <div class="mb-3">
            <strong>Model:</strong> ${info.cpu.model || 'Unknown'}
        </div>
        <div class="mb-3">
            <strong>Cores:</strong> ${info.cpu.cores || 'N/A'}
        </div>
        <div class="mb-3">
            <strong>Usage:</strong> ${info.cpu.usage_percent || 0}%
        </div>
        <div class="progress" style="height: 25px;">
            <div class="progress-bar ${getUsageColor(info.cpu.usage_percent)}" 
                 role="progressbar" 
                 style="width: ${info.cpu.usage_percent || 0}%"
                 aria-valuenow="${info.cpu.usage_percent || 0}" 
                 aria-valuemin="0" 
                 aria-valuemax="100">
                ${info.cpu.usage_percent || 0}%
            </div>
        </div>
    `;
    document.getElementById('cpuInfo').innerHTML = cpuHtml;

    // Memory Info
    const memoryHtml = `
        <div class="mb-3">
            <strong>Total:</strong> ${info.memory.total_formatted || 'N/A'}
        </div>
        <div class="mb-3">
            <strong>Used:</strong> ${info.memory.used_formatted || 'N/A'}
        </div>
        <div class="mb-3">
            <strong>Free:</strong> ${info.memory.free_formatted || 'N/A'}
        </div>
        <div class="mb-3">
            <strong>Usage:</strong> ${info.memory.usage_percent || 0}%
        </div>
        <div class="progress" style="height: 25px;">
            <div class="progress-bar ${getUsageColor(info.memory.usage_percent)}" 
                 role="progressbar" 
                 style="width: ${info.memory.usage_percent || 0}%"
                 aria-valuenow="${info.memory.usage_percent || 0}" 
                 aria-valuemin="0" 
                 aria-valuemax="100">
                ${info.memory.usage_percent || 0}%
            </div>
        </div>
    `;
    document.getElementById('memoryInfo').innerHTML = memoryHtml;

    // Storage Info
    let storageHtml = '';
    if (info.storage && info.storage.length > 0) {
        info.storage.forEach(disk => {
            storageHtml += `
                <div class="mb-4 p-3 border rounded">
                    <h6 class="mb-3">
                        <i class="fas fa-hdd me-2"></i>${disk.drive || 'Unknown Drive'}
                    </h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Total:</strong> ${disk.total_formatted || 'N/A'}
                        </div>
                        <div class="col-md-4">
                            <strong>Used:</strong> ${disk.used_formatted || 'N/A'}
                        </div>
                        <div class="col-md-4">
                            <strong>Free:</strong> ${disk.free_formatted || 'N/A'}
                        </div>
                    </div>
                    <div class="mb-2">
                        <strong>Usage:</strong> ${disk.usage_percent || 0}%
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar ${getUsageColor(disk.usage_percent)}" 
                             role="progressbar" 
                             style="width: ${disk.usage_percent || 0}%"
                             aria-valuenow="${disk.usage_percent || 0}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            ${disk.usage_percent || 0}%
                        </div>
                    </div>
                </div>
            `;
        });
    } else {
        storageHtml = '<p class="text-muted">No storage information available</p>';
    }
    document.getElementById('storageInfo').innerHTML = storageHtml;

    // Server Info
    const serverHtml = `
        <div class="mb-2"><strong>OS:</strong> ${info.server.os || 'Unknown'}</div>
        <div class="mb-2"><strong>Server Software:</strong> ${info.server.server_software || 'Unknown'}</div>
        <div class="mb-2"><strong>Hostname:</strong> ${info.server.hostname || 'Unknown'}</div>
        <div class="mb-2"><strong>IP Address:</strong> ${info.server.ip_address || 'Unknown'}</div>
        <div class="mb-2"><strong>Laravel Version:</strong> ${info.server.laravel_version || 'Unknown'}</div>
        <div class="mb-2"><strong>Uptime:</strong> ${info.server.uptime || 'Unknown'}</div>
    `;
    document.getElementById('serverInfo').innerHTML = serverHtml;

    // PHP Info
    const phpHtml = `
        <div class="mb-2"><strong>Version:</strong> ${info.php.version || 'Unknown'}</div>
        <div class="mb-2"><strong>Memory Limit:</strong> ${info.php.memory_limit || 'Unknown'}</div>
        <div class="mb-2"><strong>Max Execution Time:</strong> ${info.php.max_execution_time || 'Unknown'}s</div>
        <div class="mb-2"><strong>Upload Max Filesize:</strong> ${info.php.upload_max_filesize || 'Unknown'}</div>
        <div class="mb-2"><strong>Post Max Size:</strong> ${info.php.post_max_size || 'Unknown'}</div>
        <div class="mb-2"><strong>Timezone:</strong> ${info.php.timezone || 'Unknown'}</div>
    `;
    document.getElementById('phpInfo').innerHTML = phpHtml;
}

function getUsageColor(usage) {
    if (usage >= 90) return 'bg-danger';
    if (usage >= 70) return 'bg-warning';
    if (usage >= 50) return 'bg-info';
    return 'bg-success';
}

function refreshSystemInfo() {
    loadSystemInfo();
}

function clearCache(type) {
    const cacheTypeName = type === 'all' ? 'all cache' : `the ${type} cache`;
    
    // Use SweetAlert confirmation instead of native confirm
    Swal.fire({
        title: 'Clear Cache?',
        html: `
            <div class="text-start">
                <p class="mb-3">Are you sure you want to clear <strong>${cacheTypeName}</strong>?</p>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    <small>This action will clear the selected cache types. The system may take a moment to process.</small>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-check me-1"></i>Yes, Clear Cache',
        cancelButtonText: '<i class="fas fa-times me-1"></i>Cancel',
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        reverseButtons: true,
        focusConfirm: false
    }).then((result) => {
        if (!result.isConfirmed) {
            return;
        }

        // Show loading using SweetAlert helper
        if (typeof SwalHelpers !== 'undefined' && SwalHelpers.loading) {
            SwalHelpers.loading('Clearing Cache...', 'Please wait while we clear the cache');
        } else {
            Swal.fire({
                title: 'Clearing Cache...',
                text: 'Please wait while we clear the cache',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        fetch('{{ route("admin.clear-cache") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ type: type })
        })
        .then(async response => {
            // Check if response is ok
            if (!response.ok) {
                // Try to get error message from response
                let errorMessage = 'Network response was not ok';
                try {
                    const errorData = await response.json();
                    errorMessage = errorData.message || errorData.error || errorMessage;
                } catch (e) {
                    // If response is not JSON, get text
                    const text = await response.text();
                    errorMessage = text || errorMessage;
                }
                throw new Error(errorMessage);
            }
            
            // Check content type
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                throw new Error('Server returned non-JSON response: ' + text.substring(0, 100));
            }
            
            // Parse JSON
            return response.json();
        })
        .then(data => {
            // Close loading alert
            if (typeof SwalHelpers !== 'undefined' && SwalHelpers.close) {
                SwalHelpers.close();
            } else {
                Swal.close();
            }

            if (data.success) {
                let html = `<div class="text-start"><p class="mb-3">${data.message}</p>`;
                
                if (data.results && Object.keys(data.results).length > 0) {
                    html += '<div class="mb-3"><strong>Cache Operations:</strong><ul class="mt-2 mb-0">';
                    Object.entries(data.results).forEach(([key, value]) => {
                        const icon = value.includes('skipped') ? 'fa-info-circle text-info' : 
                                    value.includes('cleared') || value.includes('flushed') ? 'fa-check-circle text-success' : 
                                    'fa-circle text-secondary';
                        html += `<li><i class="fas ${icon} me-2"></i><strong>${key}:</strong> ${value}</li>`;
                    });
                    html += '</ul></div>';
                }
                
                if (data.warnings && Object.keys(data.warnings).length > 0) {
                    html += '<div class="alert alert-warning mb-0 mt-3"><strong><i class="fas fa-exclamation-triangle me-2"></i>Warnings:</strong><ul class="mb-0 mt-2">';
                    Object.entries(data.warnings).forEach(([key, value]) => {
                        html += `<li><strong>${key}:</strong> ${value}</li>`;
                    });
                    html += '</ul></div>';
                }
                html += '</div>';
                
                // Use SweetAlert helper if available, otherwise use direct Swal
                const hasWarnings = data.warnings && Object.keys(data.warnings).length > 0;
                Swal.fire({
                    icon: hasWarnings ? 'warning' : 'success',
                    title: hasWarnings ? 'Cache Cleared with Warnings!' : 'Cache Cleared Successfully!',
                    html: html,
                    confirmButtonText: '<i class="fas fa-check me-1"></i>OK',
                    confirmButtonColor: '#198754',
                    width: '600px',
                    timer: hasWarnings ? 5000 : 3000,
                    timerProgressBar: true
                });
            } else {
                // Use error helper if available
                if (typeof SwalHelpers !== 'undefined' && SwalHelpers.error) {
                    SwalHelpers.error(
                        'Cache Clear Failed',
                        data.message || 'Failed to clear cache'
                    );
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: `<p>${data.message || 'Failed to clear cache'}</p>${data.error ? `<small class="text-muted">${data.error}</small>` : ''}`,
                        confirmButtonColor: '#dc3545'
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Close loading alert
            if (typeof SwalHelpers !== 'undefined' && SwalHelpers.close) {
                SwalHelpers.close();
            } else {
                Swal.close();
            }
            
            let errorMessage = error.message || 'An error occurred while clearing cache';
            
            // Use error helper if available
            if (typeof SwalHelpers !== 'undefined' && SwalHelpers.error) {
                SwalHelpers.error(
                    'Cache Clear Error',
                    errorMessage
                );
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: `
                        <div class="text-start">
                            <p>An error occurred while clearing cache</p>
                            <div class="alert alert-danger mt-3 mb-0">
                                <small><strong>Details:</strong> ${errorMessage}</small>
                            </div>
                            <small class="text-muted mt-2 d-block">Please check the browser console (F12) for more details.</small>
                        </div>
                    `,
                    confirmButtonColor: '#dc3545',
                    width: '500px'
                });
            }
        });
    });
}

function showError(message) {
    const errorHtml = `
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>${message}
        </div>
    `;
    ['cpuInfo', 'memoryInfo', 'storageInfo', 'serverInfo', 'phpInfo'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.innerHTML = errorHtml;
        }
    });
}

// Clean up interval on page unload
window.addEventListener('beforeunload', function() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});
</script>
@endsection

