import './bootstrap';

/**
 * Rap-iPhone v2.0 Interactive UI Engine (Digimap Daylight Minimalist, Live Search, PDP Configurator, Inline Trade-In, Sticky Buy Bar)
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
    // 2. Daylight Frosted Toast Notification Engine
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
        toast.className = `glass-card-elevated p-4 rounded-2xl border shadow-xl flex items-center gap-3 pointer-events-auto transform transition-all duration-300 translate-y-4 opacity-0 ${
            type === 'success' 
                ? 'border-[#008040]/30 bg-white text-[#1D1D1F]' 
                : 'border-[#D70015]/30 bg-white text-[#1D1D1F]'
        }`;

        const iconHtml = type === 'success' 
            ? `<div class="w-8 h-8 rounded-xl bg-[#E8F8EE] flex items-center justify-center text-[#008040] shrink-0"><i data-lucide="check-circle" class="w-4 h-4"></i></div>`
            : `<div class="w-8 h-8 rounded-xl bg-[#FFEBEA] flex items-center justify-center text-[#D70015] shrink-0"><i data-lucide="alert-circle" class="w-4 h-4"></i></div>`;

        toast.innerHTML = `
            ${iconHtml}
            <div class="text-xs font-semibold flex-grow leading-relaxed text-[#1D1D1F]">${message}</div>
            <button type="button" class="text-[#6E6E73] hover:text-[#1D1D1F] p-1 text-sm font-bold">&times;</button>
        `;

        toastContainer.appendChild(toast);
        refreshIcons();

        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-4', 'opacity-0');
        });

        toast.querySelector('button').addEventListener('click', () => {
            toast.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => toast.remove(), 300);
        });

        setTimeout(() => {
            if (toast.isConnected) {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }
        }, 4000);
    };

    window.showGlassToast = showToast;

    // -------------------------------------------------------------------------
    // 3. Global Modal Controller (Search, Installments & Dialogs)
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
    // 4. Instant Live Search inside Modal (Daylight Styled)
    // -------------------------------------------------------------------------
    const searchModal = document.getElementById('search-modal');
    if (searchModal) {
        const searchInput = searchModal.querySelector('input[name="q"]');
        let searchResultsContainer = document.getElementById('search-modal-live-results');
        
        if (!searchResultsContainer && searchInput) {
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
                                <div class="p-3 text-center text-xs text-[#6E6E73] bg-[#F5F5F7] rounded-xl border border-[#E5E5EA]">
                                    Tidak ada hasil untuk "${query}"
                                </div>
                            `;
                            searchResultsContainer.classList.remove('hidden');
                        } else {
                            searchResultsContainer.innerHTML = data.map(item => `
                                <a href="${item.url}" class="flex items-center justify-between p-3 rounded-xl bg-[#F5F5F7] border border-[#E5E5EA] hover:border-[#0066CC] hover:bg-white transition-all group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-white border border-[#E5E5EA] flex items-center justify-center shrink-0">
                                            <i data-lucide="smartphone" class="w-4 h-4 text-[#1D1D1F]"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-[#1D1D1F] group-hover:text-[#0066CC] transition-colors">${item.name}</p>
                                            <p class="text-[10px] text-[#6E6E73]">${item.series} &bull; ${item.condition === 'second' ? 'Second Like New' : 'Garansi Resmi'}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs font-bold text-[#1D1D1F]">${item.formatted_price}</span>
                                        <span class="block text-[9px] font-semibold text-[#008040]">Ready Stock</span>
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
                        cartBadge.classList.add('scale-125', 'bg-[#008040]');
                        setTimeout(() => cartBadge.classList.remove('scale-125', 'bg-[#008040]'), 500);
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
    // 6. Product Detail Page (PDP) Interactive Configurator, Trade-In Slasher & Sticky Bar
    // -------------------------------------------------------------------------
    const pdpContainer = document.getElementById('pdp-interactive-container');
    if (pdpContainer && window.RAP_VARIANTS_DATA) {
        const variants = window.RAP_VARIANTS_DATA;
        
        let currentColor = document.querySelector('.pdp-color-dot.active')?.getAttribute('data-color-name') 
            || variants[0]?.color_name;
        let currentStorage = document.querySelector('.pdp-storage-btn.active')?.getAttribute('data-storage') 
            || variants[0]?.storage_capacity;
        let currentSellingPrice = variants[0]?.selling_price || 0;

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

        // Trade-In Slasher & Sticky Bar Elements
        const tradeInSelect = document.getElementById('pdp-tradein-select');
        const tradeInResultBox = document.getElementById('pdp-tradein-result-box');
        const tradeInCreditText = document.getElementById('pdp-tradein-credit');
        const tradeInNetPriceText = document.getElementById('pdp-net-price');
        const netPriceBadge = document.getElementById('pdp-net-price-badge');

        const stickyBar = document.getElementById('pdp-sticky-buy-bar');
        const stickyVariantLabel = document.getElementById('sticky-variant-label');
        const stickyLivePrice = document.getElementById('sticky-live-price');
        const stickyBuyNowBtn = document.getElementById('sticky-buy-now-btn');

        // Installment Modal Elements
        const simModalBasePrice = document.getElementById('sim-modal-base-price');
        const sim3m = document.getElementById('sim-3m');
        const sim6m = document.getElementById('sim-6m');
        const sim12m = document.getElementById('sim-12m');
        const sim24m = document.getElementById('sim-24m');

        const formatIdr = (num) => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.max(0, Math.round(num)));

        const updateTradeInAndInstallments = (basePrice) => {
            const credit = tradeInSelect ? (parseFloat(tradeInSelect.value) || 0) : 0;
            const netPayable = Math.max(0, basePrice - credit);

            if (tradeInResultBox && tradeInCreditText && tradeInNetPriceText) {
                if (credit > 0) {
                    tradeInResultBox.classList.remove('hidden');
                    tradeInCreditText.textContent = '- ' + formatIdr(credit);
                    tradeInNetPriceText.textContent = formatIdr(netPayable);
                    if (netPriceBadge) netPriceBadge.classList.remove('hidden');
                } else {
                    tradeInResultBox.classList.add('hidden');
                    if (netPriceBadge) netPriceBadge.classList.add('hidden');
                }
            }

            if (stickyLivePrice) {
                stickyLivePrice.textContent = credit > 0 ? formatIdr(netPayable) : formatIdr(basePrice);
            }

            // Update Installment Modal
            if (simModalBasePrice) simModalBasePrice.textContent = formatIdr(basePrice);
            if (sim3m) sim3m.textContent = formatIdr(basePrice / 3) + '/bln';
            if (sim6m) sim6m.textContent = formatIdr(basePrice / 6) + '/bln';
            if (sim12m) sim12m.textContent = formatIdr(basePrice / 12) + '/bln';
            if (sim24m) sim24m.textContent = formatIdr(basePrice / 24) + '/bln';
        };

        const updatePdpState = () => {
            let matchedVariant = variants.find(v => v.color_name === currentColor && v.storage_capacity === currentStorage);
            
            if (!matchedVariant) {
                matchedVariant = variants.find(v => v.storage_capacity === currentStorage) || variants[0];
                if (matchedVariant) {
                    currentColor = matchedVariant.color_name;
                }
            }

            if (!matchedVariant) return;

            currentSellingPrice = matchedVariant.selling_price;

            // 1. Update Hidden Variant ID Input
            if (variantIdInput) {
                variantIdInput.value = matchedVariant.id;
            }

            // 2. Instant Live Price Update
            if (priceDisplay) {
                priceDisplay.textContent = matchedVariant.selling_price_formatted;
            }

            // 3. Installment Simulation Subtext (24 months)
            if (installmentDisplay) {
                const perMonth = Math.round(matchedVariant.selling_price / 24);
                installmentDisplay.innerHTML = `Atau cicilan 0% mulai dari <strong class="text-[#1D1D1F]">Rp ${new Intl.NumberFormat('id-ID').format(perMonth)}/bln</strong> selama 24 bulan.`;
            }

            // 4. Color Labels & Glow Aura
            if (colorLabel) colorLabel.textContent = matchedVariant.color_name;
            if (vectorColorLabel) vectorColorLabel.textContent = matchedVariant.color_name;
            if (glowAura && matchedVariant.color_hex) {
                glowAura.style.backgroundColor = matchedVariant.color_hex;
            }

            // 5. Sticky Bar Label
            if (stickyVariantLabel) {
                stickyVariantLabel.textContent = `${matchedVariant.color_name} • ${matchedVariant.storage_capacity}`;
            }

            // 6. Stock Status & Quantity Controls
            if (stockStatusDisplay) {
                if (matchedVariant.stock > 0) {
                    stockStatusDisplay.textContent = `Tersedia (${matchedVariant.stock} Unit Siap Kirim)`;
                    stockStatusDisplay.className = 'text-xs font-bold text-[#008040]';
                    if (addCartBtn) addCartBtn.disabled = false;
                    if (buyNowBtn) buyNowBtn.disabled = false;
                } else {
                    stockStatusDisplay.textContent = 'Stok Habis (Pre-Order Segera)';
                    stockStatusDisplay.className = 'text-xs font-bold text-[#D70015]';
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

            // 7. Battery Health (for Pre-Owned / Second)
            if (bhPercentage && matchedVariant.battery_health) {
                bhPercentage.textContent = `${matchedVariant.battery_health}% (Prima)`;
            }

            // 8. Recalculate Trade-In & Installments
            updateTradeInAndInstallments(currentSellingPrice);

            // 9. Update Active Studio Image & Highlight Corresponding Thumbnail
            if (matchedVariant.image_url) {
                const activeImg = document.getElementById('pdp-active-image');
                if (activeImg && activeImg.src !== matchedVariant.image_url) {
                    activeImg.style.opacity = '0.7';
                    setTimeout(() => {
                        activeImg.src = matchedVariant.image_url;
                        activeImg.style.opacity = '1';
                    }, 80);
                }

                document.querySelectorAll('.pdp-thumb-btn').forEach(t => {
                    if (t.getAttribute('data-image-src') === matchedVariant.image_url) {
                        document.querySelectorAll('.pdp-thumb-btn').forEach(btn => btn.classList.remove('active', 'border-[#0066CC]'));
                        t.classList.add('active', 'border-[#0066CC]');
                    }
                });
            }
        };

        if (tradeInSelect) {
            tradeInSelect.addEventListener('change', () => {
                updateTradeInAndInstallments(currentSellingPrice);
            });
        }

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
                document.querySelectorAll('.pdp-thumb-btn').forEach(t => t.classList.remove('active', 'border-[#0066CC]'));
                this.classList.add('active', 'border-[#0066CC]');
                const imgSrc = this.getAttribute('data-image-src');
                const activeImg = document.getElementById('pdp-active-image');
                if (imgSrc && activeImg) {
                    activeImg.src = imgSrc;
                }
            });
        });

        // Initial trigger
        updatePdpState();

        // Sticky Bottom Purchase Bar Observer
        const mainCtaGroup = document.getElementById('pdp-main-cta-group');
        if (stickyBar && mainCtaGroup && 'IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (!entry.isIntersecting) {
                        stickyBar.classList.remove('translate-y-full');
                    } else {
                        stickyBar.classList.add('translate-y-full');
                    }
                });
            }, { threshold: 0.1 });
            observer.observe(mainCtaGroup);
        }

        // Add to Cart AJAX Form Submission
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
                    <div class="w-4 h-4 border-2 border-[#1D1D1F]/30 border-t-[#1D1D1F] rounded-full animate-spin"></div>
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
                        
                        const cartBadge = document.getElementById('cart-badge-count');
                        if (cartBadge) {
                            cartBadge.textContent = result.cart_count;
                            cartBadge.classList.add('scale-125', 'bg-[#008040]');
                            setTimeout(() => {
                                cartBadge.classList.remove('scale-125', 'bg-[#008040]');
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

        // Buy Now Flow (Main Button & Sticky Bar Button)
        const triggerBuyNow = () => {
            if (!window.RAP_BUY_NOW_URL) return;
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
        };

        if (buyNowBtn) buyNowBtn.addEventListener('click', triggerBuyNow);
        if (stickyBuyNowBtn) stickyBuyNowBtn.addEventListener('click', triggerBuyNow);
    }

    // -------------------------------------------------------------------------
    // 7. Shopping Cart Page Interactivity (+ / - Debounced Live Updates)
    // -------------------------------------------------------------------------
    const cartWrapper = document.getElementById('cart-content-wrapper');
    if (cartWrapper) {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        cartWrapper.querySelectorAll('.cart-qty-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const isInc = this.classList.contains('cart-qty-inc');
                const row = this.closest('.cart-item-row');
                const variantId = this.getAttribute('data-variant-id');
                const display = row.querySelector('.cart-qty-display');
                let currentVal = parseInt(display.textContent.trim()) || 1;

                let nextVal = isInc ? currentVal + 1 : currentVal - 1;
                if (nextVal < 1) return;

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
                        
                        const subtotalEl = row.querySelector('.cart-item-subtotal');
                        if (subtotalEl) subtotalEl.textContent = data.item_subtotal_formatted;

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
