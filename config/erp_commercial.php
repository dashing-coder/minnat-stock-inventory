<?php

return [
    // An unlinked return can create stock and a credit without an original invoice.
    // Keep disabled by default. Enable only if your business has a documented,
    // supervised no-receipt returns workflow with independent reconciliation.
    'allow_unlinked_returns' => env('ERP_ALLOW_UNLINKED_RETURNS', false),
];
