// Show confirmation before deleting
function confirmDelete(event, message) {
    if (!confirm(message || 'Are you sure you want to delete this item?')) {
        event.preventDefault();
        return false;
    }
    return true;
}

// Show image preview before upload
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById(previewId);
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    // Handle like button clicks
    document.querySelectorAll('.btn-like').forEach(button => {
        button.addEventListener('click', function() {
            if (!isLoggedIn) {
                window.location.href = 'login.php';
                return;
            }

            const postId = this.dataset.postId;
            const likesCount = this.querySelector('.likes-count');
            
            fetch('ajax/toggle-like.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `post_id=${postId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.classList.toggle('active');
                    likesCount.textContent = data.likes_count;
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });

    // Smooth scroll animation for links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Add loading animation to buttons
    document.querySelectorAll('.btn').forEach(button => {
        button.addEventListener('click', function() {
            if (!this.classList.contains('btn-like')) {
                this.classList.add('loading');
            }
        });
    });

    // Initialize tooltips
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Add animation to cards on scroll
    const cards = document.querySelectorAll('.card');
    const animateCards = () => {
        cards.forEach(card => {
            const cardTop = card.getBoundingClientRect().top;
            const cardBottom = card.getBoundingClientRect().bottom;
            
            if (cardTop < window.innerHeight && cardBottom > 0) {
                card.classList.add('visible');
            }
        });
    };

    window.addEventListener('scroll', animateCards);
    animateCards(); // Initial check
});

// Attach confirmation to delete links
document.querySelectorAll('.delete-link').forEach(function(link) {
    link.addEventListener('click', function(e) {
        confirmDelete(e, this.getAttribute('data-confirm-message'));
    });
});

// Attach image preview to image input fields
document.querySelectorAll('.image-input').forEach(function(input) {
    input.addEventListener('change', function() {
        const previewId = this.getAttribute('data-preview-id');
        previewImage(this, previewId);
    });
});
