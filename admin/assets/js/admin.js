/**
 * Admin JavaScript
 */

// Auto-generate slug from name
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');

    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function() {
            if (!slugInput.dataset.edited) {
                const slug = this.value
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                slugInput.value = slug;
            }
        });

        slugInput.addEventListener('input', function() {
            this.dataset.edited = 'true';
        });
    }

    // Auto-dismiss flash messages
    const flashMessages = document.querySelectorAll('.admin-message');
    flashMessages.forEach(function(msg) {
        setTimeout(function() {
            msg.style.opacity = '0';
            msg.style.transition = 'opacity 0.3s ease';
            setTimeout(function() {
                msg.remove();
            }, 300);
        }, 5000);
    });
});



