# SweetAlert2 Implementation Guide

This application uses SweetAlert2 for beautiful, responsive alerts and dialogs. Helper functions are provided for easy usage throughout the application.

## Installation

SweetAlert2 is already included in all layout files via CDN, and helper functions are available in `public/js/sweetalert-helpers.js`.

## Basic Usage

### Success Alert
```javascript
// Simple success message
SwalHelpers.success('Operation Successful!', 'Your changes have been saved.');

// With callback
SwalHelpers.success('Saved!', 'Data saved successfully', (result) => {
    console.log('User clicked OK');
    // Redirect or perform action
    window.location.href = '/dashboard';
});
```

### Error Alert
```javascript
SwalHelpers.error('Error!', 'Something went wrong. Please try again.');
```

### Warning Alert
```javascript
SwalHelpers.warning('Warning!', 'Please review your input before proceeding.');
```

### Info Alert
```javascript
SwalHelpers.info('Information', 'This feature is available in the next update.');
```

## Confirmation Dialogs

### Basic Confirmation
```javascript
SwalHelpers.confirm(
    'Are you sure?',
    'This action cannot be undone!',
    'Yes, do it!',
    'Cancel',
    (result) => {
        if (result.isConfirmed) {
            // User confirmed - perform action
            console.log('User confirmed');
        }
    }
);
```

### Delete Confirmation
```javascript
SwalHelpers.deleteConfirm(
    'Delete Item?',
    'This will permanently delete this item.',
    (result) => {
        if (result.isConfirmed) {
            // Delete the item
            deleteItem();
        }
    }
);
```

## Loading States

### Show Loading
```javascript
SwalHelpers.loading('Processing...', 'Please wait while we process your request');
```

### Close Alert
```javascript
SwalHelpers.close();
```

## Toast Notifications

Toast notifications appear in the top-right corner and auto-dismiss.

```javascript
// Success toast
SwalHelpers.successToast('Data saved successfully!');

// Error toast
SwalHelpers.errorToast('Failed to save data');

// Warning toast
SwalHelpers.warningToast('Please check your input');

// Info toast
SwalHelpers.infoToast('New update available');
```

## Form Input

```javascript
SwalHelpers.input(
    'Enter Name',
    'Name',
    'John Doe',
    'text',
    (value) => {
        console.log('User entered:', value);
        // Process the input
    }
);
```

## HTML Content

```javascript
SwalHelpers.html(
    'User Details',
    '<div class="text-start"><p><strong>Name:</strong> John Doe</p><p><strong>Email:</strong> john@example.com</p></div>',
    (result) => {
        console.log('Dialog closed');
    }
);
```

## Form Submission Handler

```javascript
SwalHelpers.handleForm(
    'myFormId',
    async (formData) => {
        // Submit form data
        const response = await fetch('/api/submit', {
            method: 'POST',
            body: formData
        });
        return await response.json();
    },
    (result) => {
        // Success callback
        console.log('Form submitted successfully');
        window.location.href = '/success';
    },
    (error) => {
        // Error callback
        console.error('Form submission failed:', error);
    }
);
```

## AJAX Request Handler

```javascript
SwalHelpers.handleAjax(
    '/api/delete-item',
    {
        method: 'POST',
        body: JSON.stringify({ id: 123 })
    },
    (data) => {
        // Success callback
        console.log('Item deleted:', data);
        location.reload();
    },
    (error) => {
        // Error callback
        console.error('Failed to delete:', error);
    }
);
```

## Advanced Usage

### Custom SweetAlert
For more advanced usage, you can still use SweetAlert2 directly:

```javascript
Swal.fire({
    title: 'Custom Alert',
    html: '<p>Custom HTML content</p>',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    confirmButtonColor: '#198754',
    cancelButtonColor: '#dc3545',
    reverseButtons: true,
    customClass: {
        popup: 'custom-popup-class'
    }
}).then((result) => {
    if (result.isConfirmed) {
        // Handle confirmation
    }
});
```

## Examples in Views

### Delete Button
```html
<button onclick="deleteItem({{ $item->id }})" class="btn btn-danger">
    Delete
</button>

<script>
function deleteItem(id) {
    SwalHelpers.deleteConfirm(
        'Delete Item?',
        'This will permanently delete this item.',
        async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/items/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        SwalHelpers.successToast('Item deleted successfully');
                        location.reload();
                    } else {
                        SwalHelpers.error('Error', data.message);
                    }
                } catch (error) {
                    SwalHelpers.error('Error', 'Failed to delete item');
                }
            }
        }
    );
}
</script>
```

### Form Submission
```html
<form id="myForm">
    <input type="text" name="name" required>
    <button type="submit">Submit</button>
</form>

<script>
SwalHelpers.handleForm(
    'myForm',
    async (formData) => {
        const response = await fetch('/api/submit', {
            method: 'POST',
            body: formData
        });
        return await response.json();
    },
    (result) => {
        SwalHelpers.successToast('Form submitted successfully');
        document.getElementById('myForm').reset();
    }
);
</script>
```

## Available Helper Functions

| Function | Description |
|----------|-------------|
| `SwalHelpers.success()` | Show success alert |
| `SwalHelpers.error()` | Show error alert |
| `SwalHelpers.warning()` | Show warning alert |
| `SwalHelpers.info()` | Show info alert |
| `SwalHelpers.confirm()` | Show confirmation dialog |
| `SwalHelpers.deleteConfirm()` | Show delete confirmation |
| `SwalHelpers.loading()` | Show loading alert |
| `SwalHelpers.close()` | Close current alert |
| `SwalHelpers.toast()` | Show toast notification |
| `SwalHelpers.successToast()` | Show success toast |
| `SwalHelpers.errorToast()` | Show error toast |
| `SwalHelpers.warningToast()` | Show warning toast |
| `SwalHelpers.infoToast()` | Show info toast |
| `SwalHelpers.input()` | Show input dialog |
| `SwalHelpers.html()` | Show HTML content |
| `SwalHelpers.handleForm()` | Handle form submission |
| `SwalHelpers.handleAjax()` | Handle AJAX requests |

## Best Practices

1. **Use appropriate alert types**: Use success for positive actions, error for failures, warning for cautions, and info for informational messages.

2. **Provide clear messages**: Always include descriptive text that explains what happened or what action is required.

3. **Use callbacks wisely**: Use callbacks to handle user responses and perform follow-up actions.

4. **Toast for non-critical messages**: Use toast notifications for non-critical messages that don't require user interaction.

5. **Loading states**: Always show loading states for async operations to provide user feedback.

6. **Error handling**: Always handle errors gracefully and provide helpful error messages.

## Browser Support

SweetAlert2 supports all modern browsers:
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Opera (latest)

## Resources

- [SweetAlert2 Documentation](https://sweetalert2.github.io/)
- [SweetAlert2 GitHub](https://github.com/sweetalert2/sweetalert2)






