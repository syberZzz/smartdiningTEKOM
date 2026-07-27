/**
 * Lumière Dining — Menu Interactivity
 * Handles: quantity controls, category filtering, search, cart management, order flow
 */

document.addEventListener('DOMContentLoaded', () => {
    // ─── State ───
    const cart = {}; // { itemId: { name, price, qty, image } }

    const formatRupiah = (num) => 'Rp' + Math.round(num).toLocaleString('id-ID');

    // ─── DOM References ───
    const menuGrid = document.getElementById('menu-grid');
    const emptyState = document.getElementById('menu-empty-state');
    const sectionTitle = document.getElementById('menu-section-title');
    const searchInput = document.getElementById('menu-search-input');
    const cartBar = document.getElementById('cart-bar');
    const cartItemCount = document.getElementById('cart-item-count');
    const cartTotalPrice = document.getElementById('cart-total-price');
    const headerCartBadge = document.getElementById('header-cart-badge');
    const categoryButtons = document.querySelectorAll('.category-btn');
    const menuCards = document.querySelectorAll('.menu-item-card');

    // Order Review Sheet DOM
    const reviewSheet = document.getElementById('order-review-sheet');
    const reviewBackdrop = document.getElementById('order-review-backdrop');
    const reviewPanel = document.getElementById('order-review-panel');
    const reviewItemsEl = document.getElementById('order-review-items');
    const reviewEmpty = document.getElementById('order-review-empty');
    const reviewTotal = document.getElementById('order-review-total');
    const notesSection = document.getElementById('order-notes-section');
    const reviewFooter = document.getElementById('order-review-footer');
    const closeReviewBtn = document.getElementById('close-review-btn');
    const placeOrderBtn = document.getElementById('place-order-btn');
    const placeOrderLoading = document.getElementById('place-order-loading');
    const orderNotes = document.getElementById('order-notes');

    let activeCategory = 'all';

    // ─── Quantity Controls ───
    menuGrid?.addEventListener('click', (e) => {
        const minusBtn = e.target.closest('.qty-minus');
        const plusBtn = e.target.closest('.qty-plus');

        if (minusBtn) {
            const itemId = minusBtn.dataset.itemId;
            directCartUpdate(itemId, -1);
        }

        if (plusBtn) {
            const itemId = plusBtn.dataset.itemId;
            directCartUpdate(itemId, 1);
        }
    });

    /**
     * Directly update cart quantity from menu card +/- buttons.
     */
    function directCartUpdate(itemId, delta) {
        const display = document.querySelector(`.qty-display[data-item-id="${itemId}"]`);
        if (!display) return;

        const card = document.querySelector(`.menu-item-card[data-item-id="${itemId}"]`);
        const qtyWrapper = display.closest('[data-name]');

        // Get item info from the qty wrapper or card
        const name = qtyWrapper?.dataset.name || card?.dataset.name || '';
        const price = parseFloat(qtyWrapper?.dataset.price || card?.dataset.price || 0);
        const image = card?.querySelector('img')?.src || '';

        // Calculate new quantity
        let currentQty = cart[itemId]?.qty || 0;
        let newQty = Math.max(0, Math.min(99, currentQty + delta));
        display.textContent = newQty;

        // Update cart state (preserve existing notes)
        if (newQty > 0) {
            cart[itemId] = { name, price, qty: newQty, image, notes: cart[itemId]?.notes || '' };
        } else {
            delete cart[itemId];
        }

        updateCartUI();
    }

    function updateCartUI() {
        let totalItems = 0;
        let totalPrice = 0;

        Object.values(cart).forEach(item => {
            totalItems += item.qty;
            totalPrice += item.price * item.qty;
        });

        // Update cart bar
        if (cartItemCount) cartItemCount.textContent = totalItems;
        if (cartTotalPrice) cartTotalPrice.textContent = formatRupiah(totalPrice);

        // Show/hide cart bar
        if (cartBar) {
            if (totalItems > 0) {
                cartBar.classList.remove('translate-y-full');
            } else {
                cartBar.classList.add('translate-y-full');
            }
        }

        // Update header cart badge
        if (headerCartBadge) {
            if (totalItems > 0) {
                headerCartBadge.classList.remove('hidden');
                headerCartBadge.textContent = totalItems;
            } else {
                headerCartBadge.classList.add('hidden');
            }
        }
    }

    // ─── Category Filter ───
    categoryButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            // Update active state
            categoryButtons.forEach(b => {
                b.classList.remove('bg-primary', 'text-on-primary', 'shadow-[0px_4px_12px_rgba(15,23,42,0.1)]');
                b.classList.add('bg-surface-container-lowest', 'text-on-surface-variant', 'border', 'border-outline-variant', 'hover:bg-surface-container');
            });
            btn.classList.remove('bg-surface-container-lowest', 'text-on-surface-variant', 'border', 'border-outline-variant', 'hover:bg-surface-container');
            btn.classList.add('bg-primary', 'text-on-primary', 'shadow-[0px_4px_12px_rgba(15,23,42,0.1)]');

            activeCategory = btn.dataset.category;

            // Update section title
            if (sectionTitle) {
                const label = btn.querySelector('.text-label-sm')?.textContent || 'Semua';
                sectionTitle.textContent = activeCategory === 'all' ? 'Semua Menu' : label;
            }

            filterItems();
        });
    });

    // ─── Search ───
    let searchTimeout;
    searchInput?.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(filterItems, 200);
    });

    function filterItems() {
        const query = searchInput?.value?.toLowerCase().trim() || '';
        let visibleCount = 0;

        menuCards.forEach(card => {
            const name = card.dataset.name || '';
            const category = card.dataset.category || '';

            const matchesSearch = !query || name.includes(query);
            const matchesCategory = activeCategory === 'all' || category === activeCategory ||
                (activeCategory === 'popular' && card.dataset.popular === 'true');

            if (matchesSearch && matchesCategory) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        // Toggle empty state
        if (emptyState) {
            if (visibleCount === 0) {
                emptyState.classList.remove('hidden');
                emptyState.classList.add('flex');
            } else {
                emptyState.classList.remove('flex');
                emptyState.classList.add('hidden');
            }
        }
    }

    // ══════════════════════════════════════════
    // ─── ORDER REVIEW SHEET ───
    // ══════════════════════════════════════════

    function openReviewSheet() {
        if (!reviewSheet) return;
        renderReviewItems();
        reviewSheet.classList.remove('pointer-events-none');
        reviewBackdrop?.classList.replace('bg-black/0', 'bg-black/40');
        reviewPanel?.classList.remove('translate-y-full');
        document.body.style.overflow = 'hidden';
    }

    function closeReviewSheet() {
        if (!reviewSheet) return;
        reviewBackdrop?.classList.replace('bg-black/40', 'bg-black/0');
        reviewPanel?.classList.add('translate-y-full');
        document.body.style.overflow = '';
        setTimeout(() => {
            reviewSheet.classList.add('pointer-events-none');
        }, 300);
    }

    function renderReviewItems() {
        if (!reviewItemsEl) return;

        const entries = Object.entries(cart);
        let totalPrice = 0;

        if (entries.length === 0) {
            reviewItemsEl.innerHTML = '';
            reviewEmpty?.classList.remove('hidden');
            reviewEmpty?.classList.add('flex');
            notesSection?.classList.add('hidden');
            reviewFooter?.classList.add('hidden');
            return;
        }

        reviewEmpty?.classList.add('hidden');
        reviewEmpty?.classList.remove('flex');
        notesSection?.classList.remove('hidden');
        reviewFooter?.classList.remove('hidden');

        reviewItemsEl.innerHTML = entries.map(([itemId, item]) => {
            const subtotal = item.price * item.qty;
            totalPrice += subtotal;
            const hasNotes = item.notes && item.notes.trim().length > 0;

            return `
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-3 review-item-card" data-review-id="${itemId}">
                    <div class="flex items-center gap-3">
                        <img src="${item.image}" alt="${item.name}" class="w-14 h-14 rounded-xl object-cover flex-shrink-0" />
                        <div class="flex-1 min-w-0">
                            <p class="text-label-md text-primary truncate">${item.name}</p>
                            <p class="text-label-sm text-on-surface-variant">${formatRupiah(item.price)}</p>
                        </div>
                        <div class="flex items-center gap-1 flex-shrink-0">
                            <button class="review-qty-minus material-symbols-outlined text-[18px] text-on-surface-variant p-1 rounded-full hover:bg-surface-container transition-colors" data-item-id="${itemId}">remove</button>
                            <span class="text-label-md w-6 text-center">${item.qty}</span>
                            <button class="review-qty-plus material-symbols-outlined text-[18px] text-on-surface-variant p-1 rounded-full hover:bg-surface-container transition-colors" data-item-id="${itemId}">add</button>
                        </div>
                        <span class="text-label-md text-primary flex-shrink-0 w-16 text-right">${formatRupiah(subtotal)}</span>
                        <button class="review-remove-item material-symbols-outlined text-[18px] text-on-surface-variant hover:text-error p-1 rounded-full hover:bg-error-container transition-colors flex-shrink-0" data-item-id="${itemId}">delete</button>
                    </div>
                    <div class="mt-2">
                        <input type="text"
                            class="review-item-notes w-full text-[12px] px-2.5 py-1.5 border border-outline-variant rounded-lg bg-surface focus:outline-none focus:border-primary transition-colors placeholder:text-on-surface-variant/60"
                            data-item-id="${itemId}"
                            placeholder="Catatan item (opsional)… cth: tidak pedas"
                            value="${(item.notes || '').replace(/"/g, '&quot;')}"
                        />
                    </div>
                </div>
            `;
        }).join('');

        if (reviewTotal) reviewTotal.textContent = formatRupiah(totalPrice);
    }

    // Review sheet event listeners
    reviewItemsEl?.addEventListener('click', (e) => {
        const minusBtn = e.target.closest('.review-qty-minus');
        const plusBtn = e.target.closest('.review-qty-plus');
        const removeBtn = e.target.closest('.review-remove-item');

        if (minusBtn) {
            const id = minusBtn.dataset.itemId;
            if (cart[id]) {
                cart[id].qty = Math.max(1, cart[id].qty - 1);
                syncMenuCardQty(id, cart[id].qty);
                updateCartUI();
                renderReviewItems();
            }
        }

        if (plusBtn) {
            const id = plusBtn.dataset.itemId;
            if (cart[id]) {
                cart[id].qty = Math.min(99, cart[id].qty + 1);
                syncMenuCardQty(id, cart[id].qty);
                updateCartUI();
                renderReviewItems();
            }
        }

        if (removeBtn) {
            const id = removeBtn.dataset.itemId;
            delete cart[id];
            syncMenuCardQty(id, 0);
            updateCartUI();
            renderReviewItems();

            // If cart is now empty, close after a short delay
            if (Object.keys(cart).length === 0) {
                setTimeout(closeReviewSheet, 400);
            }
        }
    });

    // Per-item notes: live-sync input changes to cart state
    reviewItemsEl?.addEventListener('input', (e) => {
        const notesInput = e.target.closest('.review-item-notes');
        if (notesInput) {
            const id = notesInput.dataset.itemId;
            if (cart[id]) {
                cart[id].notes = notesInput.value;
            }
        }
    });

    function syncMenuCardQty(itemId, qty) {
        const display  = document.querySelector(`.qty-display[data-item-id="${itemId}"]`);
        const minusBtn = document.querySelector(`.qty-minus[data-item-id="${itemId}"]`);

        if (display) {
            display.textContent = qty;
            qty > 0 ? display.classList.remove('hidden') : display.classList.add('hidden');
        }
        if (minusBtn) {
            if (qty > 0) {
                minusBtn.classList.remove('hidden');
                minusBtn.classList.add('flex');
            } else {
                minusBtn.classList.add('hidden');
                minusBtn.classList.remove('flex');
            }
        }
    }

    // Open/close review sheet
    document.getElementById('view-order-btn')?.addEventListener('click', openReviewSheet);
    closeReviewBtn?.addEventListener('click', closeReviewSheet);
    reviewBackdrop?.addEventListener('click', closeReviewSheet);

    // ══════════════════════════════════════════
    // ─── MINI MENU SHEET (Tambah Item Lagi) ───
    // 2 sheet terpisah: review sheet & mini-menu sheet
    // ══════════════════════════════════════════

    const miniMenuSheet    = document.getElementById('mini-menu-sheet');
    const miniMenuBackdrop = document.getElementById('mini-menu-backdrop');
    const miniMenuPanel    = document.getElementById('mini-menu-panel');
    const miniMenuSearch   = document.getElementById('mini-menu-search');
    const miniMenuList     = document.getElementById('mini-menu-list');
    const backToReviewBtn  = document.getElementById('back-to-review-btn');

    function openMiniMenu() {
        // Tutup review sheet (tanpa restore overflow dulu)
        reviewBackdrop?.classList.replace('bg-black/40', 'bg-black/0');
        reviewPanel?.classList.add('translate-y-full');
        setTimeout(() => reviewSheet?.classList.add('pointer-events-none'), 300);

        // Sinkron qty, reset search, buka mini-menu sheet
        syncMiniMenuQty();
        if (miniMenuSearch) { miniMenuSearch.value = ''; filterMiniMenu(''); }

        miniMenuSheet?.classList.remove('pointer-events-none');
        miniMenuBackdrop?.classList.replace('bg-black/0', 'bg-black/40');
        miniMenuPanel?.classList.remove('translate-y-full');
        document.body.style.overflow = 'hidden';
    }

    function closeMiniMenu() {
        // Tutup mini-menu sheet
        miniMenuBackdrop?.classList.replace('bg-black/40', 'bg-black/0');
        miniMenuPanel?.classList.add('translate-y-full');
        setTimeout(() => {
            miniMenuSheet?.classList.add('pointer-events-none');
            // Buka kembali review sheet
            openReviewSheet();
        }, 300);
    }

    // Sinkronkan tampilan qty mini-menu dengan state cart
    function syncMiniMenuQty() {
        document.querySelectorAll('.mini-menu-item').forEach(card => {
            const id      = card.dataset.itemId;
            const qty     = cart[id]?.qty || 0;
            const qtyEl   = card.querySelector('.mini-qty-display');
            const minusEl = card.querySelector('.mini-qty-minus');
            if (qtyEl) qtyEl.textContent = qty;
            if (qty > 0) {
                qtyEl?.classList.remove('hidden');
                minusEl?.classList.remove('hidden');
                minusEl?.classList.add('flex');
            } else {
                qtyEl?.classList.add('hidden');
                minusEl?.classList.add('hidden');
                minusEl?.classList.remove('flex');
            }
        });
    }

    function filterMiniMenu(query) {
        document.querySelectorAll('.mini-menu-item').forEach(card => {
            card.style.display = (!query || card.dataset.name.includes(query)) ? '' : 'none';
        });
    }

    // Qty controls di mini-menu list
    miniMenuList?.addEventListener('click', (e) => {
        const plus  = e.target.closest('.mini-qty-plus');
        const minus = e.target.closest('.mini-qty-minus');
        if (!plus && !minus) return;

        const itemId  = (plus || minus).dataset.itemId;
        const card    = miniMenuList.querySelector(`.mini-menu-item[data-item-id="${itemId}"]`);
        if (!card) return;

        const qtyEl   = card.querySelector('.mini-qty-display');
        const minusEl = card.querySelector('.mini-qty-minus');
        const name    = card.dataset.displayName;
        const price   = parseFloat(card.dataset.price);
        const image   = card.dataset.image;

        let current = cart[itemId]?.qty || 0;
        if (plus)  current = Math.min(99, current + 1);
        if (minus) current = Math.max(0, current - 1);

        if (current > 0) {
            cart[itemId] = { name, price, qty: current, image, notes: cart[itemId]?.notes || '' };
        } else {
            delete cart[itemId];
        }

        if (qtyEl) qtyEl.textContent = current;
        if (current > 0) {
            qtyEl?.classList.remove('hidden');
            minusEl?.classList.remove('hidden');
            minusEl?.classList.add('flex');
        } else {
            qtyEl?.classList.add('hidden');
            minusEl?.classList.add('hidden');
            minusEl?.classList.remove('flex');
        }

        // Sinkron ke menu grid utama
        syncMenuCardQty(itemId, current);
        updateCartUI();
    });

    // Search di mini-menu
    miniMenuSearch?.addEventListener('input', () => {
        filterMiniMenu(miniMenuSearch.value.toLowerCase().trim());
    });

    // Backdrop mini-menu (tap luar = tutup, kembali ke review)
    miniMenuBackdrop?.addEventListener('click', closeMiniMenu);

    // "Tambah Item Lagi" → buka mini-menu sheet
    document.getElementById('add-more-items-btn')?.addEventListener('click', openMiniMenu);

    // "← Back" di mini-menu → kembali ke review sheet
    backToReviewBtn?.addEventListener('click', closeMiniMenu);

    // Header cart button
    document.getElementById('header-cart-btn')?.addEventListener('click', () => {
        if (Object.keys(cart).length > 0) {
            openReviewSheet();
        }
    });

    // ─── Place Order ───
    placeOrderBtn?.addEventListener('click', async () => {
        const entries = Object.entries(cart);
        if (entries.length === 0) return;

        // Show loading
        placeOrderBtn.classList.add('hidden');
        placeOrderLoading?.classList.remove('hidden');
        placeOrderLoading?.classList.add('flex');

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        const tableNumber = document.querySelector('meta[name="table-number"]')?.content || '';

        const payload = {
            table_number: tableNumber,
            notes: orderNotes?.value?.trim() || null,
            items: entries.map(([itemId, item]) => ({
                menu_item_id: parseInt(itemId),
                quantity: item.qty,
                notes: item.notes?.trim() || '',
            })),
        };

        try {
            const response = await fetch('/orders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json();

            if (data.success && data.redirect) {
                window.location.href = data.redirect;
            } else {
                throw new Error(data.errors ? Object.values(data.errors).flat().join(', ') : 'Order gagal');
            }
        } catch (error) {
            // Reset loading state
            placeOrderBtn?.classList.remove('hidden');
            placeOrderLoading?.classList.add('hidden');
            placeOrderLoading?.classList.remove('flex');

            // Show a simple error notification
            showNotification('Gagal memproses pesanan. Silakan coba lagi.', 'error');
            console.error('Order error:', error);
        }
    });

    // ─── Simple Notification ───
    function showNotification(message, type = 'info') {
        const existing = document.getElementById('app-notification');
        if (existing) existing.remove();

        const bgColor = type === 'error' ? 'bg-error text-on-error' : 'bg-primary text-on-primary';

        const notification = document.createElement('div');
        notification.id = 'app-notification';
        notification.className = `fixed top-4 left-1/2 -translate-x-1/2 z-[200] ${bgColor} px-6 py-3 rounded-full text-label-md shadow-[0px_4px_16px_rgba(15,23,42,0.2)] animate-slide-down`;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.add('animate-slide-up');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
});
