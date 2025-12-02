/**
 * SweetAlert2 Helper Functions
 * Provides convenient wrapper functions for common alert patterns
 */

// Check if SweetAlert2 is loaded
if (typeof Swal === 'undefined') {
    console.error('SweetAlert2 is not loaded. Please include SweetAlert2 library.');
}

/**
 * Show success alert
 */
function showSuccess(title, message, callback) {
    return Swal.fire({
        icon: 'success',
        title: title || 'Success!',
        text: message || '',
        confirmButtonColor: '#198754',
        timer: 3000,
        timerProgressBar: true
    }).then((result) => {
        if (callback && typeof callback === 'function') {
            callback(result);
        }
        return result;
    });
}

/**
 * Show error alert
 */
function showError(title, message, callback) {
    return Swal.fire({
        icon: 'error',
        title: title || 'Error!',
        text: message || 'An error occurred',
        confirmButtonColor: '#dc3545'
    }).then((result) => {
        if (callback && typeof callback === 'function') {
            callback(result);
        }
        return result;
    });
}

/**
 * Show warning alert
 */
function showWarning(title, message, callback) {
    return Swal.fire({
        icon: 'warning',
        title: title || 'Warning!',
        text: message || '',
        confirmButtonColor: '#ffc107',
        confirmButtonText: 'OK'
    }).then((result) => {
        if (callback && typeof callback === 'function') {
            callback(result);
        }
        return result;
    });
}

/**
 * Show info alert
 */
function showInfo(title, message, callback) {
    return Swal.fire({
        icon: 'info',
        title: title || 'Information',
        text: message || '',
        confirmButtonColor: '#0dcaf0'
    }).then((result) => {
        if (callback && typeof callback === 'function') {
            callback(result);
        }
        return result;
    });
}

/**
 * Show confirmation dialog
 */
function showConfirm(title, message, confirmText, cancelText, callback) {
    return Swal.fire({
        title: title || 'Are you sure?',
        text: message || "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: confirmText || 'Yes, do it!',
        cancelButtonText: cancelText || 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed && callback && typeof callback === 'function') {
            callback(result);
        }
        return result;
    });
}

/**
 * Show delete confirmation
 */
function showDeleteConfirm(title, message, callback) {
    return showConfirm(
        title || 'Delete?',
        message || 'This action cannot be undone!',
        'Yes, delete it!',
        'Cancel',
        callback
    );
}

/**
 * Show loading alert
 */
function showLoading(title, text) {
    Swal.fire({
        title: title || 'Loading...',
        text: text || 'Please wait',
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

/**
 * Close current alert
 */
function closeAlert() {
    Swal.close();
}

/**
 * Show toast notification
 */
function showToast(icon, title, timer) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: timer || 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    return Toast.fire({
        icon: icon || 'success',
        title: title || 'Action completed'
    });
}

/**
 * Show success toast
 */
function showSuccessToast(title) {
    return showToast('success', title);
}

/**
 * Show error toast
 */
function showErrorToast(title) {
    return showToast('error', title);
}

/**
 * Show warning toast
 */
function showWarningToast(title) {
    return showToast('warning', title);
}

/**
 * Show info toast
 */
function showInfoToast(title) {
    return showToast('info', title);
}

/**
 * Show form input dialog
 */
function showInput(title, inputLabel, inputPlaceholder, inputType, callback) {
    return Swal.fire({
        title: title || 'Input',
        input: inputType || 'text',
        inputLabel: inputLabel || 'Enter value',
        inputPlaceholder: inputPlaceholder || '',
        inputValidator: (value) => {
            if (!value) {
                return 'You need to write something!';
            }
        },
        showCancelButton: true,
        confirmButtonText: 'Submit',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed && callback && typeof callback === 'function') {
            callback(result.value);
        }
        return result;
    });
}

/**
 * Show HTML content in alert
 */
function showHTML(title, html, callback) {
    return Swal.fire({
        title: title || '',
        html: html || '',
        showCloseButton: true,
        showCancelButton: false,
        confirmButtonText: 'OK',
        confirmButtonColor: '#198754',
        width: '600px'
    }).then((result) => {
        if (callback && typeof callback === 'function') {
            callback(result);
        }
        return result;
    });
}

/**
 * Handle form submission with loading state
 */
function handleFormSubmit(formId, submitCallback, successCallback, errorCallback) {
    const form = document.getElementById(formId);
    if (!form) {
        console.error('Form not found:', formId);
        return;
    }

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Show loading
        showLoading('Processing...', 'Please wait');

        try {
            const formData = new FormData(form);
            const result = await submitCallback(formData);

            closeAlert();

            if (result.success) {
                if (successCallback) {
                    successCallback(result);
                } else {
                    showSuccess('Success!', result.message || 'Operation completed successfully', () => {
                        if (result.redirect) {
                            window.location.href = result.redirect;
                        } else {
                            location.reload();
                        }
                    });
                }
            } else {
                if (errorCallback) {
                    errorCallback(result);
                } else {
                    showError('Error!', result.message || 'An error occurred');
                }
            }
        } catch (error) {
            closeAlert();
            if (errorCallback) {
                errorCallback({ success: false, message: error.message });
            } else {
                showError('Error!', error.message || 'An error occurred');
            }
        }
    });
}

/**
 * Handle AJAX request with SweetAlert
 */
async function handleAjaxRequest(url, options, successCallback, errorCallback) {
    showLoading('Processing...', 'Please wait');

    try {
        const response = await fetch(url, {
            ...options,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Content-Type': 'application/json',
                ...options.headers
            }
        });

        // Handle 419 CSRF Token Mismatch
        if (response.status === 419) {
            closeAlert();
            
            // Silently refresh the page to get a new CSRF token
            Swal.fire({
                icon: 'info',
                title: 'Refreshing...',
                text: 'Please wait while we refresh your session.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Reload page after a short delay to get new CSRF token
            setTimeout(() => {
                window.location.reload();
            }, 500);
            
            return { success: false, message: 'CSRF token expired. Page will refresh.' };
        }

        // Handle 401 Unauthorized (session expired)
        if (response.status === 401) {
            closeAlert();
            const data = await response.json().catch(() => ({}));
            
            // Show session expired message and redirect to login
            Swal.fire({
                icon: 'warning',
                title: 'Session Expired',
                text: data.message || 'Your session has expired. Please log in again.',
                confirmButtonText: 'Go to Login',
                confirmButtonColor: '#3085d6',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then(() => {
                window.location.href = '/login';
            });
            
            return { success: false, message: data.message || 'Session expired' };
        }

        const data = await response.json();
        closeAlert();

        if (response.ok && data.success) {
            if (successCallback) {
                successCallback(data);
            } else {
                showSuccess('Success!', data.message || 'Operation completed successfully');
            }
            return data;
        } else {
            if (errorCallback) {
                errorCallback(data);
            } else {
                showError('Error!', data.message || 'An error occurred');
            }
            return data;
        }
    } catch (error) {
        closeAlert();
        if (errorCallback) {
            errorCallback({ success: false, message: error.message });
        } else {
            showError('Error!', error.message || 'Network error occurred');
        }
        throw error;
    }
}

// Export functions to window for global access
window.SwalHelpers = {
    success: showSuccess,
    error: showError,
    warning: showWarning,
    info: showInfo,
    confirm: showConfirm,
    deleteConfirm: showDeleteConfirm,
    loading: showLoading,
    close: closeAlert,
    toast: showToast,
    successToast: showSuccessToast,
    errorToast: showErrorToast,
    warningToast: showWarningToast,
    infoToast: showInfoToast,
    input: showInput,
    html: showHTML,
    handleForm: handleFormSubmit,
    handleAjax: handleAjaxRequest
};





