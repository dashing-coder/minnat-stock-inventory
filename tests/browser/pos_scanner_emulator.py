"""Optional browser-level HID scanner simulation (NOT a physical scanner acceptance test).
Run: python tests/browser/pos_scanner_emulator.py
Requires playwright Python and a local Chromium installation.
"""
from pathlib import Path
import json
import os
import shutil
from playwright.sync_api import sync_playwright

project = Path(__file__).resolve().parents[2]
blade = (project / 'resources/views/pos/index.blade.php').read_text(encoding='utf-8')
inline = blade.split('<script>')[-1].split('</script>')[0]
products = [
    {'id':1, 'name':'Phone <HD>', 'sku':'SKU-A', 'barcode':'8900000000001', 'code':'PRD-1',
     'retail':100, 'wholesale':80, 'special':75, 'taxRate':7,
     'stocks':{'1':10, '2':1}},
    {'id':2, 'name':'Cable', 'sku':'SKU-B', 'barcode':'12345', 'code':'PRD-2',
     'retail':20, 'wholesale':15, 'special':10, 'taxRate':0,
     'stocks':{'1':5, '2':4}},
]
inline = (inline.replace('@json($productData)', json.dumps(products))
         .replace("@json(old('items', []))", '[]')
         .replace('@json($autoTaxEnabled)', 'true'))
core_js = (project / 'public/js/afrin-pos-core.js').read_text(encoding='utf-8')
html = '''<!doctype html><html><head><meta charset="utf-8"></head><body>
<form id="pos-form"><input id="pos-scan"><div id="pos-suggestions"></div>
<select id="pos-warehouse"><option value="1">Main</option><option value="2">Secondary</option></select>
<select id="pos-customer"><option value="">Walk-in</option></select>
<table><tbody id="pos-cart"></tbody></table><span id="pos-count"></span><span id="pos-subtotal"></span>
<span id="pos-tax"></span><span id="pos-total"></span><div id="pos-error"></div>
<button id="pos-submit" type="submit">Complete Sale</button></form><script>''' + core_js + '</script><script>' + inline + '</script></body></html>'

with sync_playwright() as p:
    browser_path = os.getenv('PLAYWRIGHT_CHROMIUM_PATH') or shutil.which('chromium')
    options = {'executable_path': browser_path} if browser_path else {}
    browser = p.chromium.launch(headless=True, args=['--no-sandbox', '--disable-dev-shm-usage'], **options)
    page = browser.new_page()
    errors = []
    page.on('pageerror', lambda e: errors.append(str(e)))
    page.set_content(html)
    assert page.locator('#pos-submit').is_disabled()
    page.locator('#pos-scan').focus()
    page.keyboard.type('8900000000001', delay=2)
    page.keyboard.press('Enter')
    assert page.locator('#pos-cart tr').count() == 1
    page.keyboard.type('8900000000001', delay=2)
    page.keyboard.press('Tab')
    assert page.locator('.pos-qty').input_value() == '2'
    assert page.locator('#pos-total').inner_text() == '214.00'
    page.locator('.pos-price').fill('50.00')
    assert page.locator('.pos-price').evaluate('(el) => document.activeElement === el')
    assert page.locator('#pos-total').inner_text() == '107.00'
    page.locator('.pos-free').fill('9')
    assert page.locator('#pos-submit').is_disabled()
    page.locator('.pos-free').fill('0')
    assert page.locator('#pos-submit').is_enabled()
    page.locator('#pos-warehouse').select_option('2')
    assert page.locator('#pos-submit').is_disabled()
    page.locator('#pos-scan').fill('UNKNOWN')
    page.keyboard.press('Enter')
    assert page.locator('#pos-submit').is_disabled()
    assert not errors, errors
    browser.close()
print('PASS: browser-emulated HID Enter/Tab, repeated scan, tax, typing focus, stock checks, warehouse switch, unknown code.')
