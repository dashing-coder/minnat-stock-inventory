(function () {
    'use strict';

    // Global singleton: prevents the same shared script from initializing twice.
    if (window.__AFRIN_SEARCHABLE_SELECT_V5__) {
        return;
    }
    window.__AFRIN_SEARCHABLE_SELECT_V5__ = true;

    const script = document.currentScript;
    const i18n = {
        customerSearch: script?.dataset.customerSearch || 'Search customer...',
        productSearch: script?.dataset.productSearch || 'Search product...',
        noCustomer: script?.dataset.noCustomer || 'No matching customers found.',
        noProduct: script?.dataset.noProduct || 'No matching products found.',
        supplierSearch: script?.dataset.supplierSearch || 'Search supplier...',
        noSupplier: script?.dataset.noSupplier || 'No matching suppliers found.',
    };

    // Covers Customer, Product, and Supplier fields, including nested/dynamic rows.
    const selector = 'select[name*="customer_id"], select[name*="product_id"], select[name*="supplier_id"]';
    const instances = new Set();
    let uid = 0;

    function kindOf(select) {
        const name = select.getAttribute('name') || '';
        if (name.includes('customer_id')) return 'customer';
        if (name.includes('supplier_id')) return 'supplier';
        return 'product';
    }

    function normalize(value) {
        return String(value || '').toLocaleLowerCase().normalize('NFKD');
    }

    function closeAll(except = null) {
        instances.forEach(instance => {
            if (instance !== except) instance.close();
        });
    }

    function cleanupDisconnected() {
        instances.forEach(instance => {
            if (!instance.wrapper.isConnected) {
                instance.menu.remove();
                instances.delete(instance);
            }
        });
    }

    function ownWrapper(select) {
        const wrapper = select.closest('.erp-search-select[data-afrin-search="v5"]');
        return wrapper && wrapper.contains(select) ? wrapper : null;
    }

    /**
     * Remove a legacy/stale enhancement around this select before V5 takes over.
     * This specifically fixes the "double Customer / double Product" control that
     * can happen when an older cached enhancer and the global enhancer both run.
     */
    function removeLegacyWrappers(select) {
        if (ownWrapper(select)) return;

        const wrappers = [];
        let parent = select.parentElement;

        while (parent) {
            if (parent.classList?.contains('erp-search-select')) {
                wrappers.push(parent);
            }
            parent = parent.parentElement;
        }

        if (!wrappers.length) {
            select.classList.remove('erp-search-select__native');
            delete select.dataset.erpSearchEnhanced;
            return;
        }

        const outermost = wrappers[wrappers.length - 1];
        const host = outermost.parentNode;

        if (host) {
            host.insertBefore(select, outermost);
            outermost.remove();
        }

        select.classList.remove('erp-search-select__native');
        delete select.dataset.erpSearchEnhanced;
    }

    /**
     * Defensive repair if another older script tries to wrap a V5 control later.
     * Keep the V5 wrapper and remove the extra nested wrapper/toggle.
     */
    function repairNestedWrapper(select) {
        const keeper = ownWrapper(select);
        if (!keeper) return;

        const wrappers = [];
        let node = select.parentElement;

        while (node) {
            if (node.classList?.contains('erp-search-select')) {
                wrappers.push(node);
            }
            node = node.parentElement;
        }

        if (wrappers.length <= 1) return;

        const keeperToggle = keeper.querySelector(':scope > .erp-search-select__toggle');
        if (select.parentElement !== keeper) {
            keeper.insertBefore(select, keeperToggle || keeper.firstChild);
        }

        wrappers.forEach(wrapper => {
            if (wrapper === keeper) return;

            if (wrapper.contains(keeper)) {
                const host = wrapper.parentNode;
                if (host) host.insertBefore(keeper, wrapper);
                wrapper.remove();
            } else if (keeper.contains(wrapper)) {
                wrapper.remove();
            }
        });

        // Remove menus created by stale/legacy wrappers. V5 menus are marked.
        document.querySelectorAll('.erp-search-select__menu:not([data-afrin-search="v5"])')
            .forEach(menu => menu.remove());
    }

    function enhance(select) {
        if (!(select instanceof HTMLSelectElement)) return;

        // Already enhanced by this version: never create a second visible field.
        if (select.dataset.erpSearchEnhanced === 'v5' && ownWrapper(select)) {
            repairNestedWrapper(select);
            return;
        }

        removeLegacyWrappers(select);

        const kind = kindOf(select);
        const id = `afrin-search-${++uid}`;
        const wrapper = document.createElement('div');
        wrapper.className = 'erp-search-select';
        wrapper.dataset.afrinSearch = 'v5';
        wrapper.dataset.afrinSearchId = id;

        select.parentNode.insertBefore(wrapper, select);
        wrapper.appendChild(select);
        select.classList.add('erp-search-select__native');
        select.dataset.erpSearchEnhanced = 'v5';

        const toggle = document.createElement('button');
        toggle.type = 'button';
        toggle.className = 'erp-search-select__toggle';
        toggle.setAttribute('aria-haspopup', 'listbox');
        toggle.setAttribute('aria-expanded', 'false');

        const toggleText = document.createElement('span');
        toggleText.className = 'erp-search-select__toggle-text';
        const caret = document.createElement('span');
        caret.className = 'erp-search-select__caret';
        caret.innerHTML = '&#9662;';
        toggle.append(toggleText, caret);
        wrapper.appendChild(toggle);

        // Menu is mounted on body so overflow/table containers cannot clip it.
        const menu = document.createElement('div');
        menu.className = 'erp-search-select__menu';
        menu.dataset.afrinSearch = 'v5';
        menu.dataset.afrinSearchId = id;

        const searchWrap = document.createElement('div');
        searchWrap.className = 'erp-search-select__search-wrap';
        const search = document.createElement('input');
        search.type = 'search';
        search.className = 'erp-search-select__search';
        search.autocomplete = 'off';
        search.placeholder = kind === 'customer'
            ? i18n.customerSearch
            : (kind === 'supplier' ? i18n.supplierSearch : i18n.productSearch);
        search.setAttribute('aria-label', search.placeholder);
        searchWrap.appendChild(search);

        const optionsBox = document.createElement('div');
        optionsBox.className = 'erp-search-select__options';
        optionsBox.setAttribute('role', 'listbox');

        menu.append(searchWrap, optionsBox);
        document.body.appendChild(menu);

        let instance;

        function selectedLabel() {
            const option = select.options[select.selectedIndex];
            return option ? option.textContent.trim() : '';
        }

        function positionMenu() {
            if (!wrapper.classList.contains('open')) return;

            const rect = toggle.getBoundingClientRect();
            const viewportPadding = 8;
            const width = Math.max(rect.width, 220);
            let left = rect.left;

            if (left + width > window.innerWidth - viewportPadding) {
                left = Math.max(viewportPadding, window.innerWidth - width - viewportPadding);
            }

            menu.style.left = `${left}px`;
            menu.style.width = `${Math.min(width, window.innerWidth - viewportPadding * 2)}px`;
            menu.style.top = `${rect.bottom + 5}px`;

            requestAnimationFrame(() => {
                const menuRect = menu.getBoundingClientRect();
                if (menuRect.bottom > window.innerHeight - viewportPadding && rect.top > menuRect.height + viewportPadding) {
                    menu.style.top = `${Math.max(viewportPadding, rect.top - menuRect.height - 5)}px`;
                }
            });
        }

        function render(term) {
            const query = normalize(term).trim();
            optionsBox.innerHTML = '';
            let count = 0;

            Array.from(select.options).forEach(option => {
                if (option.disabled) return;

                const label = option.textContent.trim();
                if (query && !normalize(label).includes(query)) return;

                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'erp-search-select__option';
                item.textContent = label;
                item.dataset.value = option.value;
                item.setAttribute('role', 'option');
                item.setAttribute('aria-selected', option.selected ? 'true' : 'false');

                if (option.selected) item.classList.add('active');

                item.addEventListener('click', () => choose(option.value));
                optionsBox.appendChild(item);
                count++;
            });

            if (!count) {
                const empty = document.createElement('div');
                empty.className = 'erp-search-select__empty';
                empty.textContent = kind === 'customer'
                    ? i18n.noCustomer
                    : (kind === 'supplier' ? i18n.noSupplier : i18n.noProduct);
                optionsBox.appendChild(empty);
            }
        }

        function sync() {
            toggleText.textContent = selectedLabel();
            const disabled = select.disabled;
            toggle.disabled = disabled;
            wrapper.classList.toggle('is-disabled', disabled);
            wrapper.classList.toggle('has-error', !select.checkValidity());

            if (wrapper.classList.contains('open')) {
                render(search.value);
            }
        }

        function choose(value) {
            select.value = value;
            select.dispatchEvent(new Event('change', { bubbles: true }));
            instance.close();
            search.value = '';
            sync();
            toggle.focus();
        }

        function open() {
            if (select.disabled) return;

            closeAll(instance);
            wrapper.classList.add('open');
            menu.classList.add('open');
            toggle.setAttribute('aria-expanded', 'true');
            search.value = '';
            render('');
            positionMenu();
            requestAnimationFrame(() => search.focus());
        }

        function close() {
            wrapper.classList.remove('open');
            menu.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        }

        instance = { wrapper, menu, select, open, close, positionMenu };
        instances.add(instance);

        toggle.addEventListener('click', () => {
            wrapper.classList.contains('open') ? close() : open();
        });

        search.addEventListener('input', () => {
            render(search.value);
            positionMenu();
        });

        search.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                close();
                toggle.focus();
            } else if (event.key === 'Enter') {
                const first = optionsBox.querySelector('.erp-search-select__option');
                if (first) {
                    event.preventDefault();
                    choose(first.dataset.value);
                }
            }
        });

        select.addEventListener('change', sync);
        select.addEventListener('invalid', event => {
            event.preventDefault();
            wrapper.classList.add('has-error');
            toggle.focus();
        });

        const attributeObserver = new MutationObserver(() => {
            repairNestedWrapper(select);
            sync();
        });
        attributeObserver.observe(select, {
            attributes: true,
            attributeFilter: ['disabled'],
            childList: true,
            subtree: true,
        });

        sync();
    }

    function scan(root) {
        if (root instanceof HTMLSelectElement && root.matches(selector)) {
            enhance(root);
        }

        if (root.querySelectorAll) {
            root.querySelectorAll(selector).forEach(enhance);
        }
    }

    document.addEventListener('click', event => {
        const inside = Array.from(instances).some(instance =>
            instance.wrapper.contains(event.target) || instance.menu.contains(event.target)
        );

        if (!inside) closeAll();
    });

    window.addEventListener('resize', () => {
        instances.forEach(instance => instance.positionMenu());
    });

    window.addEventListener('scroll', () => {
        instances.forEach(instance => instance.positionMenu());
    }, true);

    document.addEventListener('DOMContentLoaded', () => scan(document));

    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            mutation.addedNodes.forEach(node => {
                if (node.nodeType === Node.ELEMENT_NODE) scan(node);
            });
        });

        // A legacy enhancer running after V5 can create nested wrappers. Repair
        // them automatically so only one visible Customer/Product control remains.
        document.querySelectorAll(selector).forEach(repairNestedWrapper);
        cleanupDisconnected();
    });

    observer.observe(document.documentElement, { childList: true, subtree: true });
})();
