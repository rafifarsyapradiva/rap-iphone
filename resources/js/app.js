import './bootstrap';

/**
 * Rap-iPhone Interactive UI Helpers (Glassmorphism Modal, Search, Color Dots, Cart Badge)
 */
document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize Lucide Icons if available via CDN
    if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons();
    }

    // 2. Global Modal Controller
    const openModal = (modalId) => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
            const input = modal.querySelector('input');
            if (input) setTimeout(() => input.focus(), 100);
        }
    };

    const closeModal = (modal) => {
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    };

    // Modal Trigger Buttons
    document.querySelectorAll('[data-modal-target]').forEach(trigger => {
        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            const modalId = trigger.getAttribute('data-modal-target');
            openModal(modalId);
        });
    });

    // Modal Close Buttons
    document.querySelectorAll('[data-modal-close]').forEach(closeBtn => {
        closeBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const modal = closeBtn.closest('.glass-modal-backdrop');
            closeModal(modal);
        });
    });

    // Close Modal on Backdrop Click
    document.querySelectorAll('.glass-modal-backdrop').forEach(backdrop => {
        backdrop.addEventListener('click', (e) => {
            if (e.target === backdrop) {
                closeModal(backdrop);
            }
        });
    });

    // Keyboard Shortcuts: Ctrl+K / Cmd+K for Search Modal, Esc to close
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            const searchModal = document.getElementById('search-modal');
            if (searchModal) {
                if (searchModal.classList.contains('active')) {
                    closeModal(searchModal);
                } else {
                    openModal('search-modal');
                }
            }
        } else if (e.key === 'Escape') {
            document.querySelectorAll('.glass-modal-backdrop.active').forEach(modal => {
                closeModal(modal);
            });
        }
    });

    // 3. Admin Layout Mobile Sidebar Toggle
    const sidebarToggleBtn = document.getElementById('admin-sidebar-toggle');
    const adminSidebar = document.getElementById('admin-sidebar');
    if (sidebarToggleBtn && adminSidebar) {
        sidebarToggleBtn.addEventListener('click', () => {
            adminSidebar.classList.toggle('translate-x-0');
            adminSidebar.classList.toggle('-translate-x-full');
        });
    }

    // 4. Color Dot Picker in Product Cards
    document.querySelectorAll('.color-dot-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const parent = this.closest('.product-card');
            if (!parent) return;

            // Deselect other dots in same card
            parent.querySelectorAll('.color-dot-btn').forEach(d => d.classList.remove('active'));
            this.classList.add('active');

            // Update color label if present
            const label = parent.querySelector('.selected-color-label');
            const colorName = this.getAttribute('data-color-name');
            if (label && colorName) {
                label.textContent = colorName;
            }
        });
    });
});
