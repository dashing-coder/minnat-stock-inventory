<?php
/** Framework-independent checks; complements PHPUnit and real scanner acceptance. */
$root = dirname(__DIR__);
require $root.'/app/Services/Barcode/Code39Barcode.php';
require $root.'/app/Services/Barcode/BarcodeCatalogService.php';
$barcode = new \App\Services\Barcode\Code39Barcode();
$catalog = new \App\Services\Barcode\BarcodeCatalogService();
$checks = [
    'Normalized scan and Code 39 delimiters' => $catalog->scanKey(" *abc123*\r\n") === 'ABC123',
    'Exact numeric barcode preserved' => $barcode->normalize('8900000000001') === '8900000000001',
    'SVG black bars present' => str_contains($barcode->svg('ABC123'), '<rect'),
    'Label width protection' => $barcode->minimumLabelWidthMm('8900000000001') >= 51,
    'Product and variant validation integrated' => str_contains(file_get_contents($root.'/app/Http/Requests/ProductRequest.php'),'BarcodeCatalogService')
        && str_contains(file_get_contents($root.'/app/Http/Controllers/ProductVariantController.php'),'BarcodeCatalogService'),
    'Printer collision guard' => str_contains(file_get_contents($root.'/app/Http/Controllers/BarcodePrintController.php'),'firstCollision'),
    'Read-only catalogue audit registered' => str_contains(file_get_contents($root.'/routes/console.php'),'erp:barcode:audit'),
];
foreach ($checks as $label => $ok) {
    echo ($ok ? 'PASS' : 'FAIL').": {$label}\n";
}
exit(in_array(false,$checks,true) ? 1 : 0);
