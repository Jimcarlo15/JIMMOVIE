/**
 * JimMovie - Interactive JavaScript
 * Handles modals, client-side fast filtering, rating preview, and UI interactions
 */

document.addEventListener('DOMContentLoaded', function () {
    initModals();
    initRatingSliderSync();
    initPosterPreview();
});

// Modal Management
function openModal(id) {
    var modal = document.getElementById(id);
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        var firstInput = modal.querySelector('input[type="text"]');
        if (firstInput) {
            firstInput.focus();
        }
    }
}

function closeModal(id) {
    var modal = document.getElementById(id);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

function initModals() {
    // Close modal on click outside box
    document.querySelectorAll('.catalog-modal-overlay').forEach(function (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) {
                overlay.style.display = 'none';
                document.body.style.overflow = '';
            }
        });
    });

    // Close on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.catalog-modal-overlay').forEach(function (overlay) {
                overlay.style.display = 'none';
            });
            document.body.style.overflow = '';
        }
    });
}

// Live Rating value check
function initRatingSliderSync() {
    var ratingInputs = document.querySelectorAll('input[name="rating"]');
    ratingInputs.forEach(function (input) {
        input.addEventListener('input', function () {
            var val = parseFloat(input.value);
            if (val > 10) input.value = 10;
            if (val < 0) input.value = 0;
        });
    });
}

// Poster URL preview inside form
function initPosterPreview() {
    var posterInputs = document.querySelectorAll('input[name="poster_url"]');
    posterInputs.forEach(function (input) {
        input.addEventListener('change', function () {
            var form = input.closest('form');
            if (!form) return;
            var previewContainer = form.querySelector('.poster-live-preview');
            if (!previewContainer) {
                previewContainer = document.createElement('div');
                previewContainer.className = 'poster-live-preview';
                previewContainer.style.marginTop = '8px';
                previewContainer.style.textAlign = 'center';
                input.parentNode.appendChild(previewContainer);
            }
            if (input.value.trim() !== '') {
                previewContainer.innerHTML = '<img src="' + input.value + '" style="max-height:120px; border:1px solid #ff9900; border-radius:3px;" alt="Poster Preview" onerror="this.style.display=\'none\'">';
            } else {
                previewContainer.innerHTML = '';
            }
        });
    });
}
