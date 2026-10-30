const test = require('node:test');
const assert = require('node:assert/strict');
const {normalizeScan, indexProducts, lookup, calculate} = require('../../public/js/afrin-pos-core.js');
const a = {id: 1, name: 'Phone', barcode: '8900000000001', sku: 'SKU-A', code: 'PRD-1', taxRate: 7.5,
    stocks: {'1': 10, '2': 2}, retail: 100, wholesale: 90};
const b = {id: 2, name: 'Cable', barcode: '12345678', sku: 'SKU-B', code: 'PRD-2', taxRate: 0,
    stocks: {'1': 5}, retail: 20};
const item = (product, qty, free=0, unit=100) => ({product, qty, free, unit, type:'retail'});

test('USB scanner with CR/whitespace and optional Code39 framing', () => {
    const index = indexProducts([a,b]);
    assert.equal(lookup(index, '8900000000001\r').product.id, 1);
    assert.equal(lookup(index, '*sku-a*\n').product.id, 1);
    assert.equal(lookup(index, 'PRD-2\t').product.id, 2);
    assert.equal(normalizeScan('  sku-b '), 'SKU-B');
});
test('unknown barcode and duplicate barcode/SKU collision are not assigned to an arbitrary product', () => {
    const duplicate = {...b, sku: 'sku-a'};
    const index = indexProducts([a, duplicate]);
    assert.equal(lookup(index, 'no-match').product, null);
    assert.equal(lookup(index, 'sku-a').ambiguous, true);
    assert.equal(lookup(index, '12345678').product.id, 2);
});
test('POS billing matches line-level auto tax and correct free stock depletion', () => {
    const result = calculate([item(a, 2, 1, 100),item(b, 1, 0, 20)], '1', true);
    assert.equal(result.subtotal, 220);
    assert.equal(result.tax, 15);
    assert.equal(result.total, 235);
    assert.equal(result.units, 3);
    assert.equal(result.valid, true);
});
test('zero-tax mode and fractional quantities retain server-compatible rounding', () => {
    const result = calculate([item(a, 0.3333, 0, 99.99)], '1', false);
    assert.equal(result.subtotal, 33.33);
    assert.equal(result.tax, 0);
    assert.equal(result.total, 33.33);
    assert.equal(result.valid, true);
});
test('warehouse stock cap includes free quantity and duplicate scan lines', () => {
    assert.equal(calculate([item(a, 2, 1)], '2', true).valid, false);
    assert.equal(calculate([item(a, 6), item(a, 5)], '1', true).valid, false);
});
test('invalid amounts block checkout', () => {
    for (const cart of [[],[item(a,0)],[item(a,-1)],[item(a,1.00001)],[item(a,1,0,-1)],[item(a,1,0,12.345)],[item(a,1,0,0)]]) {
        assert.equal(calculate(cart,'1',true).valid, false);
    }
});
