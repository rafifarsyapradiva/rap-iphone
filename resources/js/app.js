import './bootstrap';

/**
 * Rap-iPhone Interactive UI Engine (Glassmorphism, Cart, Live Search, PDP Configurator)
 */
document.addEventListener('DOMContentLoaded', () => {
    // -------------------------------------------------------------------------
    // 1. Initialize Lucide Icons
    // -------------------------------------------------------------------------
    const refreshIcons = () => {
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    };
    refreshIcons();

    // -------------------------------------------------------------------------
    // 2. Glassmorphism Toast Notification Engine
    // -------------------------------------------------------------------------
    const showToast = (message, type = 'success') => {
        let toastContainer = document.getElementById('glass-toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'glass-toast-container';
            toastContainer.className = 'fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 max-w-sm pointer-events-none';
            document.body.appendChild(toastContainer);
        }

        const toast = document.createElement('div');
        toast.className = `glass-card-elevated p-4 rounded-2xl border shadow-2xl flex items-center gap-3 pointer-events-auto transform transition-all duration-300 translate-y-4 opacity-0 ${
            type === 'success' 
                ? 'border-[#34C759]/40 bg-[#161B22]/95 text-white' 
                : 'border-[#FF453A]/40 bg-[#161B22]/95 text-white'
        }`;

        const iconHtml = type === 'success' 
            ? `<div class="w-8 h-8 rounded-xl bg-[#34C759]/20 flex items-center justify-center text-[#34C759] shrink-0"><i data-lucide="check-circle" class="w-4 h-4"></i></div>`
            : `<div class="w-8 h-8 rounded-xl bg-[#FF453A]/20 flex items-center justify-center text-[#FF453A] shrink-0"><i data-lucide="alert-circle" class="w-4 h-4"></i></div>`;

        toast.innerHTML = `
            ${iconHtml}
            <div class="text-xs font-medium flex-grow leading-relaxed">${message}</div>
            <button type="button" class="text-[#8E8E93] hover:text-white p-1 text-sm font-bold">&times;</button>
        `;

        toastContainer.appendChild(toast);
        refreshIcons();

        // Animate in
        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-4', 'opacity-0');
        });

        // Close on button click
        toast.querySelector('button').addEventListener('click', () => {
            toast.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => toast.remove(), 300);
        });

        // Auto dismiss
        setTimeout(() => {
            if (toast.isConnected) {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }
        }, 4000);
    };

    // Global helper
    window.showGlassToast = showToast;

    // -------------------------------------------------------------------------
    // 3. Global Modal Controller (Search & Dialogs)
    // -------------------------------------------------------------------------
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

    document.querySelectorAll('[data-modal-target]').forEach(trigger => {
        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            const modalId = trigger.getAttribute('data-modal-target');
            openModal(modalId);
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach(closeBtn => {
        closeBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const modal = closeBtn.closest('.glass-modal-backdrop');
            closeModal(modal);
        });
    });

    document.querySelectorAll('.glass-modal-backdrop').forEach(backdrop => {
        backdrop.addEventListener('click', (e) => {
            if (e.target === backdrop) {
                closeModal(backdrop);
            }
        });
    });

    // Keyboard Shortcuts: Ctrl+K / Cmd+K
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

    // -------------------------------------------------------------------------
    // 4. Instant Live Search inside Modal
    // -------------------------------------------------------------------------
    const searchModal = document.getElementById('search-modal');
    if (searchModal) {
        const searchInput = searchModal.querySelector('input[name="q"]');
        let searchResultsContainer = document.getElementById('search-modal-live-results');
        
        if (!searchResultsContainer) {
            searchResultsContainer = document.createElement('div');
            searchResultsContainer.id = 'search-modal-live-results';
            searchResultsContainer.className = 'mt-4 space-y-2 max-h-64 overflow-y-auto hidden';
            searchInput.closest('form').after(searchResultsContainer);
        }

        let debounceTimeout = null;

        if (searchInput) {
            searchInput.addEventListener('input', () => {
                const query = searchInput.value.trim();
                clearTimeout(debounceTimeout);

                if (query.length < 2) {
                    searchResultsContainer.innerHTML = '';
                    searchResultsContainer.classList.add('hidden');
                    return;
                }

                debounceTimeout = setTimeout(async () => {
                    try {
                        const response = await fetch(`/api/products/search?q=${encodeURIComponent(query)}`);
                        if (!response.ok) return;
                        const data = await response.json();

                        if (data.length === 0) {
                            searchResultsContainer.innerHTML = `
                                <div class="p-3 text-center text-xs text-[#8E8E93] glass-card rounded-xl">
                                    Tidak ada hasil untuk "${query}"
                                </div>
                            `;
                            searchResultsContainer.classList.remove('hidden');
                        } else {
                            searchResultsContainer.innerHTML = data.map(item => `
                                <a href="${item.url}" class="flex items-center justify-between p-3 rounded-xl glass-card hover:border-[#0071E3] transition-all group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center shrink-0">
                                            <i data-lucide="smartphone" class="w-4 h-4 text-white"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-white group-hover:text-[#5AC8FA] transition-colors">${item.name}</p>
                                            <p class="text-[10px] text-[#8E8E93]">${item.series} · ${item.condition === 'second' ? 'Second Like New' : 'Garansi Resmi'}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs font-bold text-white">${item.formatted_price}</span>
                                        <span class="block text-[9px] text-[#34C759]">Ready Stock</span>
                                    </div>
                                </a>
                            `).join('');
                            searchResultsContainer.classList.remove('hidden');
                            refreshIcons();
                        }
                    } catch (err) {
                        console.error('Search error:', err);
                    }
                }, 250);
            });
        }
    }

    // -------------------------------------------------------------------------
    // 5. Color Dot Picker in Product Cards (Home & Catalog)
    // -------------------------------------------------------------------------
    document.querySelectorAll('.color-dot-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const parent = this.closest('.product-card');
            if (!parent) return;

            parent.querySelectorAll('.color-dot-btn').forEach(d => d.classList.remove('active'));
            this.classList.add('active');

            const label = parent.querySelector('.selected-color-label');
            const colorName = this.getAttribute('data-color-name');
            if (label && colorName) {
                label.textContent = colorName;
            }
        });
    });

    // Quick Add to Cart from Product Cards (AJAX)
    document.querySelectorAll('.quick-add-cart-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const variantId = this.querySelector('input[name="product_variant_id"]')?.value;
            const quantity = this.querySelector('input[name="quantity"]')?.value || 1;

            if (btn) btn.disabled = true;

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        product_variant_id: variantId,
                        quantity: quantity
                    })
                });

                const data = await response.json();
                if (response.ok && data.success) {
                    showToast(data.message, 'success');
                    const cartBadge = document.getElementById('cart-badge-count');
                    if (cartBadge) {
                        cartBadge.textContent = data.cart_count;
                        cartBadge.classList.add('scale-125', 'bg-[#34C759]');
                        setTimeout(() => cartBadge.classList.remove('scale-125', 'bg-[#34C759]'), 500);
                    }
                } else {
                    showToast(data.message || 'Gagal menambahkan unit.', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Terjadi kesalahan jaringan.', 'error');
            } finally {
                if (btn) btn.disabled = false;
            }
        });
    });

    // -------------------------------------------------------------------------
    // 6. Product Detail Page (PDP) Interactive Configurator
    // -------------------------------------------------------------------------
    const pdpContainer = document.getElementById('pdp-interactive-container');
    if (pdpContainer && window.RAP_VARIANTS_DATA) {
        const variants = window.RAP_VARIANTS_DATA;
        
        let currentColor = document.querySelector('.pdp-color-dot.active')?.getAttribute('data-color-name') 
            || variants[0]?.color_name;
        let currentStorage = document.querySelector('.pdp-storage-btn.active')?.getAttribute('data-storage') 
            || variants[0]?.storage_capacity;

        const variantIdInput = document.getElementById('selected-variant-id');
        const priceDisplay = document.getElementById('pdp-live-price');
        const installmentDisplay = document.getElementById('pdp-installment-calc');
        const stockStatusDisplay = document.getElementById('pdp-stock-status');
        const quantityInput = document.getElementById('pdp-quantity-input');
        const colorLabel = document.getElementById('pdp-active-color-label');
        const vectorColorLabel = document.getElementById('pdp-vector-color-name');
        const glowAura = document.getElementById('pdp-glow-aura');
        const bhPercentage = document.getElementById('pdp-bh-percentage');
        const addCartBtn = document.getElementById('pdp-add-cart-btn');
        const buyNowBtn = document.getElementById('pdp-buy-now-btn');

        // Function to find the matching variant and update state
        const updatePdpState = () => {
            // Find variant with matching color and storage
            let matchedVariant = variants.find(v => v.color_name === currentColor && v.storage_capacity === currentStorage);
            
            // If combination not found, fall back to first variant with matching storage or color
            if (!matchedVariant) {
                matchedVariant = variants.find(v => v.storage_capacity === currentStorage) || variants[0];
                if (matchedVariant) {
                    currentColor = matchedVariant.color_name;
                }
            }

            if (!matchedVariant) return;

            // 1. Update Hidden Variant ID Input
            if (variantIdInput) {
                variantIdInput.value = matchedVariant.id;
            }

            // 2. Instant Live Price Update
            if (priceDisplay) {
                priceDisplay.textContent = matchedVariant.selling_price_formatted;
            }

            // 3. Installment Simulation Update (24 months)
            if (installmentDisplay) {
                const perMonth = Math.round(matchedVariant.selling_price / 24);
                installmentDisplay.innerHTML = `Cicilan 0% mulai dari <strong class="text-[#5AC8FA]">Rp ${new Intl.NumberFormat('id-ID').format(perMonth)}/bln</strong> dengan kartu kredit mitra.`;
            }

            // 4. Color Labels & Glow Aura
            if (colorLabel) colorLabel.textContent = matchedVariant.color_name;
            if (vectorColorLabel) vectorColorLabel.textContent = matchedVariant.color_name;
            if (glowAura && matchedVariant.color_hex) {
                glowAura.style.backgroundColor = matchedVariant.color_hex;
            }

            // 5. Stock Status & Quantity Controls
            if (stockStatusDisplay) {
                if (matchedVariant.stock > 0) {
                    stockStatusDisplay.textContent = `Tersedia (${matchedVariant.stock} Unit Siap Kirim)`;
                    stockStatusDisplay.className = 'text-xs font-bold text-[#34C759]';
                    if (addCartBtn) addCartBtn.disabled = false;
                    if (buyNowBtn) buyNowBtn.disabled = false;
                } else {
                    stockStatusDisplay.textContent = 'Stok Habis (Pre-Order Segera)';
                    stockStatusDisplay.className = 'text-xs font-bold text-[#FF453A]';
                    if (addCartBtn) addCartBtn.disabled = true;
                    if (buyNowBtn) buyNowBtn.disabled = true;
                }
            }

            if (quantityInput) {
                quantityInput.max = matchedVariant.stock;
                if (parseInt(quantityInput.value) > matchedVariant.stock) {
                    quantityInput.value = Math.max(1, matchedVariant.stock);
                }
            }

            // 6. Battery Health (for Pre-Owned / Second)
            if (bhPercentage && matchedVariant.battery_health) {
                bhPercentage.textContent = `${matchedVariant.battery_health}%`;
            }
        };

        // Color Dot Clicks
        document.querySelectorAll('.pdp-color-dot').forEach(dot => {
            dot.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.pdp-color-dot').forEach(d => d.classList.remove('active'));
                this.classList.add('active');
                currentColor = this.getAttribute('data-color-name');
                updatePdpState();
            });
        });

        // Storage Chip Clicks
        document.querySelectorAll('.pdp-storage-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.pdp-storage-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentStorage = this.getAttribute('data-storage');
                updatePdpState();
            });
        });

        // Quantity Stepper
        const decBtn = document.getElementById('pdp-qty-dec');
        const incBtn = document.getElementById('pdp-qty-inc');

        if (decBtn && quantityInput) {
            decBtn.addEventListener('click', () => {
                let val = parseInt(quantityInput.value) || 1;
                if (val > 1) {
                    quantityInput.value = val - 1;
                }
            });
        }

        if (incBtn && quantityInput) {
            incBtn.addEventListener('click', () => {
                let val = parseInt(quantityInput.value) || 1;
                let max = parseInt(quantityInput.max) || 99;
                if (val < max) {
                    quantityInput.value = val + 1;
                }
            });
        }

        // Thumbnails Gallery Switcher
        document.querySelectorAll('.pdp-thumb-btn').forEach(thumb => {
            thumb.addEventListener('click', function() {
                document.querySelectorAll('.pdp-thumb-btn').forEach(t => t.classList.remove('active', 'border-[#0071E3]'));
                this.classList.add('active', 'border-[#0071E3]');
                const imgSrc = this.getAttribute('data-image-src');
                const activeImg = document.getElementById('pdp-active-image');
                if (imgSrc && activeImg) {
                    activeImg.src = imgSrc;
                }
            });
        });

        // Initial trigger
        updatePdpState();

        // ---------------------------------------------------------------------
        // Add to Cart AJAX Form Submission
        // ---------------------------------------------------------------------
        const pdpForm = document.getElementById('pdp-buy-form');
        if (pdpForm) {
            pdpForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const variantId = variantIdInput.value;
                const qty = quantityInput ? parseInt(quantityInput.value) : 1;

                if (!variantId) {
                    showToast('Silakan pilih varian unit terlebih dahulu.', 'error');
                    return;
                }

                const originalBtnText = addCartBtn.innerHTML;
                addCartBtn.disabled = true;
                addCartBtn.innerHTML = `
                    <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                    <span>Menambahkan...</span>
                `;

                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const response = await fetch(pdpForm.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            product_variant_id: variantId,
                            quantity: qty,
                        })
                    });

                    const result = await response.json();

                    if (response.ok && result.success) {
                        showToast(result.message, 'success');
                        
                        // Bounce & Update Cart Badge
                        const cartBadge = document.getElementById('cart-badge-count');
                        if (cartBadge) {
                            cartBadge.textContent = result.cart_count;
                            cartBadge.classList.remove('animate-pulse');
                            void cartBadge.offsetWidth; // trigger reflow
                            cartBadge.classList.add('scale-125', 'bg-[#34C759]');
                            setTimeout(() => {
                                cartBadge.classList.remove('scale-125', 'bg-[#34C759]');
                                cartBadge.classList.add('animate-pulse');
                            }, 500);
                        }
                    } else {
                        showToast(result.message || 'Gagal menambahkan unit ke keranjang.', 'error');
                    }
                } catch (err) {
                    console.error('Add to cart error:', err);
                    showToast('Terjadi gangguan jaringan. Coba beberapa saat lagi.', 'error');
                } finally {
                    addCartBtn.disabled = false;
                    addCartBtn.innerHTML = originalBtnText;
                    refreshIcons();
                }
            });
        }

        // ---------------------------------------------------------------------
        // Buy Now Button (Direct Checkout Flow)
        // ---------------------------------------------------------------------
        if (buyNowBtn && window.RAP_BUY_NOW_URL) {
            buyNowBtn.addEventListener('click', () => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = window.RAP_BUY_NOW_URL;

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                form.appendChild(csrfInput);

                const variantInput = document.createElement('input');
                variantInput.type = 'hidden';
                variantInput.name = 'product_variant_id';
                variantInput.value = variantIdInput.value;
                form.appendChild(variantInput);

                const qtyInput = document.createElement('input');
                qtyInput.type = 'hidden';
                qtyInput.name = 'quantity';
                qtyInput.value = quantityInput ? quantityInput.value : 1;
                form.appendChild(qtyInput);

                document.body.appendChild(form);
                form.submit();
            });
        }
    }

    // -------------------------------------------------------------------------
    // 7. Shopping Cart Page Interactivity (+ / - Debounced Live Updates)
    // -------------------------------------------------------------------------
    const cartWrapper = document.getElementById('cart-content-wrapper');
    if (cartWrapper) {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Handle Quantity Buttons
        cartWrapper.querySelectorAll('.cart-qty-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const isInc = this.classList.contains('cart-qty-inc');
                const row = this.closest('.cart-item-row');
                const variantId = this.getAttribute('data-variant-id');
                const display = row.querySelector('.cart-qty-display');
                let currentVal = parseInt(display.textContent.trim()) || 1;

                let nextVal = isInc ? currentVal + 1 : currentVal - 1;
                if (nextVal < 1) return;

                // Disable during update
                row.querySelectorAll('.cart-qty-btn').forEach(b => b.disabled = true);

                try {
                    const response = await fetch(`/cart/update/${variantId}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ quantity: nextVal })
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        display.textContent = data.quantity;
                        
                        // Update Subtotal in row
                        const subtotalEl = row.querySelector('.cart-item-subtotal');
                        if (subtotalEl) subtotalEl.textContent = data.item_subtotal_formatted;

                        // Update Cart Summary
                        const summarySubtotal = document.getElementById('cart-summary-subtotal');
                        const summaryGrandTotal = document.getElementById('cart-summary-grand-total');
                        const summaryTotalQty = document.getElementById('cart-summary-total-qty');
                        const cartBadge = document.getElementById('cart-badge-count');

                        if (summarySubtotal) summarySubtotal.textContent = data.grand_total_formatted;
                        if (summaryGrandTotal) summaryGrandTotal.textContent = data.grand_total_formatted;
                        if (summaryTotalQty) summaryTotalQty.textContent = `${data.cart_count} Unit`;
                        if (cartBadge) cartBadge.textContent = data.cart_count;

                        showToast('Kuantitas berhasil diperbarui.', 'success');
                    } else {
                        showToast(data.message || 'Gagal mengubah kuantitas.', 'error');
                    }
                } catch (err) {
                    console.error('Cart update error:', err);
                    showToast('Gagal menghubungi server.', 'error');
                } finally {
                    row.querySelectorAll('.cart-qty-btn').forEach(b => b.disabled = false);
                }
            });
        });

        // Handle AJAX Cart Removal with smooth row fadeout
        cartWrapper.querySelectorAll('.cart-remove-form').forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                if (!confirm('Hapus item ini dari keranjang belanja?')) return;

                const row = this.closest('.cart-item-row');
                const actionUrl = this.action;

                try {
                    const response = await fetch(actionUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                        }
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        row.classList.add('opacity-0', 'scale-95', 'transition-all', 'duration-300');
                        setTimeout(() => {
                            row.remove();
                            if (data.is_empty) {
                                window.location.reload();
                            } else {
                                const summarySubtotal = document.getElementById('cart-summary-subtotal');
                                const summaryGrandTotal = document.getElementById('cart-summary-grand-total');
                                const summaryTotalQty = document.getElementById('cart-summary-total-qty');
                                const cartBadge = document.getElementById('cart-badge-count');

                                if (summarySubtotal) summarySubtotal.textContent = data.grand_total_formatted;
                                if (summaryGrandTotal) summaryGrandTotal.textContent = data.grand_total_formatted;
                                if (summaryTotalQty) summaryTotalQty.textContent = `${data.cart_count} Unit`;
                                if (cartBadge) cartBadge.textContent = data.cart_count;
                            }
                        }, 300);

                        showToast(data.message, 'success');
                    } else {
                        showToast('Gagal menghapus item.', 'error');
                    }
                } catch (err) {
                    console.error('Cart remove error:', err);
                    showToast('Gagal menghapus item.', 'error');
                }
            });
        });
    }

    // -------------------------------------------------------------------------
    // 8. Admin Sidebar Toggle & Customer Mobile Navigation Drawer
    // -------------------------------------------------------------------------
    const sidebarToggleBtn = document.getElementById('admin-sidebar-toggle');
    const adminSidebar = document.getElementById('admin-sidebar');
    const adminBackdrop = document.getElementById('admin-sidebar-backdrop');

    if (sidebarToggleBtn && adminSidebar) {
        const toggleAdminSidebar = () => {
            const isOpening = adminSidebar.classList.contains('-translate-x-full');
            adminSidebar.classList.toggle('translate-x-0', isOpening);
            adminSidebar.classList.toggle('-translate-x-full', !isOpening);
            if (adminBackdrop) {
                adminBackdrop.classList.toggle('hidden', !isOpening);
            }
        };

        sidebarToggleBtn.addEventListener('click', toggleAdminSidebar);
        if (adminBackdrop) {
            adminBackdrop.addEventListener('click', toggleAdminSidebar);
        }
    }

    const mobileNavToggle = document.getElementById('mobile-nav-toggle');
    const mobileNavDrawer = document.getElementById('mobile-nav-drawer');
    if (mobileNavToggle && mobileNavDrawer) {
        mobileNavToggle.addEventListener('click', () => {
            const isHidden = mobileNavDrawer.classList.toggle('hidden');
            mobileNavToggle.setAttribute('aria-expanded', (!isHidden).toString());
        });
    }
});
