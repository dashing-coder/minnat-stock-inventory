/* POS core: shared by the checkout screen and scanner/billing regression tests. */
(function (root, factory) {
    const api = factory();
    if (typeof module === 'object' && module.exports) module.exports = api;
    root.AfrinPosCore = api;
})(typeof window !== 'undefined' ? window : globalThis, function () {
    'use strict';
    function normalizeScan(value) {
        let code = String(value ?? '').trim().toUpperCase();
        if (code.length > 2 && code.startsWith('*') && code.endsWith('*')) code = code.slice(1, -1);
        return code;
    }
    function indexProducts(products) {
        const index = new Map();
        for (const product of products) {
            const codes = new Set([product.barcode, product.sku, product.code]
                .filter(v => String(v ?? '').trim() !== '').map(normalizeScan));
            for (const code of codes) {
                if (!index.has(code)) index.set(code, {product, ambiguous: false});
                else if (index.get(code).product?.id !== product.id) index.set(code, {product: null, ambiguous: true});
            }
        }
        return index;
    }
    function lookup(index, raw) {
        const code = normalizeScan(raw);
        return code ? (index.get(code) ?? {product: null, ambiguous: false}) : {product: null, ambiguous: false};
    }
    const roundMoney = number => Math.round((number + Number.EPSILON) * 100) / 100;
    const validPlaces = (number, places) => Math.abs(number - Number(number.toFixed(places))) < 1e-8;
    function calculate(cart, warehouseId, autoTaxEnabled) {
        let subtotal = 0, tax = 0, units = 0;
        const requested = new Map();
        const errors = [];
        const lines = cart.map((item, i) => {
            const qty = Number(item.qty), free = Number(item.free), unit = Number(item.unit);
            if (![qty, free, unit].every(Number.isFinite) || qty <= 0 || free < 0 || unit < 0 ||
                !validPlaces(qty, 4) || !validPlaces(free, 4) || !validPlaces(unit, 2)) {
                errors.push(`Invalid quantity or price on line ${i + 1}.`);
            }
            requested.set(item.product.id, (requested.get(item.product.id) ?? 0) + qty + free);
            const amount = roundMoney(qty * unit);
            const lineTax = autoTaxEnabled ? roundMoney(qty * unit * Number(item.product.taxRate ?? 0) / 100) : 0;
            subtotal += qty * unit;
            tax += lineTax;
            units += qty;
            return {amount, lineTax, total: roundMoney(amount + lineTax)};
        });
        if (cart.length && subtotal < 0.005) {
            errors.push('POS checkout total must be greater than zero.');
        }
        for (const item of cart) {
            const available = Number(item.product.stocks?.[String(warehouseId)] ?? 0);
            const count = requested.get(item.product.id);
            if (count > available + 1e-7) {
                errors.push(`${item.product.name} exceeds available warehouse stock (${available.toFixed(4)}).`);
                requested.delete(item.product.id);
            }
        }
        return {lines, subtotal: roundMoney(subtotal), tax: roundMoney(tax),
            total: roundMoney(subtotal + tax), units, errors, valid: cart.length > 0 && errors.length === 0};
    }
    return {normalizeScan, indexProducts, lookup, calculate};
});
