<?php

use App\Http\Controllers\{AccountController,AuditLogController,BackupController,BarcodePrintController,BrandController,BusinessDayController,CategoryController,ClientSetupController,CustomerController,CustomerGroupController,CustomerLedgerController,CustomerPaymentController,DashboardController,EmployeeController,ExpenseCategoryController,ExpenseController,InventoryController,JournalController,LoanController,MarketHierarchyController,MoneyTransactionController,NotificationController,OpeningStockController,PayrollController,PayrollPaymentController,PosController,ProductController,ProductDeliveryController,ProductReturnHubController,ProductSetupController,ProductVariantController,ProductionControlController,PurchaseController,PurchaseReturnController,QuotationController,ReorderLevelController,ReportController,RoleController,SaleController,SaleReturnController,SellerAccountController,SettingsController,StockAdjustmentController,StockLedgerController,StockTransferController,SupplierController,SupplierGroupController,SupplierLedgerController,SupplierPaymentController,UnitController,UserController,UserModuleController,WarehouseController};
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/language/{locale}', function (Request $request, string $locale) {
    abort_unless(in_array($locale, ['en', 'bn'], true), 404);

    $request->session()->put('locale', $locale);
    app()->setLocale($locale);

    return redirect()->back();
})->name('language.switch');

Route::redirect('/', '/dashboard');
Route::middleware('guest')->group(function(){Route::get('/login',[LoginController::class,'create'])->name('login');Route::post('/login',[LoginController::class,'store'])->name('login.store');});

Route::middleware(['auth','active'])->group(function(){
    Route::post('/logout',[LoginController::class,'destroy'])->name('logout');
    Route::get('/dashboard',DashboardController::class)->name('dashboard');

    // Administration
    Route::get('/users',[UserController::class,'index'])->middleware('permission:users.view')->name('users.index');
    Route::get('/users/create',[UserController::class,'create'])->middleware('permission:users.create')->name('users.create');
    Route::post('/users',[UserController::class,'store'])->middleware('permission:users.create')->name('users.store');
    Route::get('/users/{user}/edit',[UserController::class,'edit'])->middleware('permission:users.edit')->name('users.edit');
    Route::put('/users/{user}',[UserController::class,'update'])->middleware('permission:users.edit')->name('users.update');
    Route::delete('/users/{user}',[UserController::class,'destroy'])->middleware('permission:users.delete')->name('users.destroy');

    Route::get('/roles',[RoleController::class,'index'])->middleware('permission:roles.view')->name('roles.index');
    Route::get('/roles/create',[RoleController::class,'create'])->middleware('permission:roles.create')->name('roles.create');
    Route::post('/roles',[RoleController::class,'store'])->middleware('permission:roles.create')->name('roles.store');
    Route::get('/roles/{role}/edit',[RoleController::class,'edit'])->middleware('permission:roles.edit')->name('roles.edit');
    Route::put('/roles/{role}',[RoleController::class,'update'])->middleware('permission:roles.edit')->name('roles.update');
    Route::delete('/roles/{role}',[RoleController::class,'destroy'])->middleware('permission:roles.delete')->name('roles.destroy');

    // Seller accounts (Admin/Super Admin only through permission assignment)
    Route::get('/seller-accounts',[SellerAccountController::class,'index'])->middleware('permission:seller_accounts.view')->name('seller-accounts.index');
    Route::get('/seller-accounts/{seller}',[SellerAccountController::class,'show'])->middleware('permission:seller_accounts.view')->name('seller-accounts.show');

    // Customer master
    Route::get('/customer-groups',[CustomerGroupController::class,'index'])->middleware('permission:customer_groups.view')->name('customer-groups.index');
    Route::get('/customer-groups/create',[CustomerGroupController::class,'create'])->middleware('permission:customer_groups.create')->name('customer-groups.create');
    Route::post('/customer-groups',[CustomerGroupController::class,'store'])->middleware('permission:customer_groups.create')->name('customer-groups.store');
    Route::get('/customer-groups/{customerGroup}/edit',[CustomerGroupController::class,'edit'])->middleware('permission:customer_groups.edit')->name('customer-groups.edit');
    Route::put('/customer-groups/{customerGroup}',[CustomerGroupController::class,'update'])->middleware('permission:customer_groups.edit')->name('customer-groups.update');
    Route::delete('/customer-groups/{customerGroup}',[CustomerGroupController::class,'destroy'])->middleware('permission:customer_groups.delete')->name('customer-groups.destroy');
    Route::get('/customers',[CustomerController::class,'index'])->middleware('permission:customers.view')->name('customers.index');
    Route::get('/customers/create',[CustomerController::class,'create'])->middleware('permission:customers.create')->name('customers.create');
    Route::post('/customers',[CustomerController::class,'store'])->middleware('permission:customers.create')->name('customers.store');
    Route::get('/customers/{customer}',[CustomerController::class,'show'])->middleware('permission:customers.view')->name('customers.show');
    Route::get('/customers/{customer}/edit',[CustomerController::class,'edit'])->middleware('permission:customers.edit')->name('customers.edit');
    Route::put('/customers/{customer}',[CustomerController::class,'update'])->middleware('permission:customers.edit')->name('customers.update');
    Route::delete('/customers/{customer}',[CustomerController::class,'destroy'])->middleware('permission:customers.delete')->name('customers.destroy');
    Route::get('/customers/{customer}/ledger',[CustomerLedgerController::class,'show'])->middleware('permission:customers.ledger')->name('customers.ledger');

    // Supplier master
    Route::get('/supplier-groups',[SupplierGroupController::class,'index'])->middleware('permission:supplier_groups.view')->name('supplier-groups.index');
    Route::get('/supplier-groups/create',[SupplierGroupController::class,'create'])->middleware('permission:supplier_groups.create')->name('supplier-groups.create');
    Route::post('/supplier-groups',[SupplierGroupController::class,'store'])->middleware('permission:supplier_groups.create')->name('supplier-groups.store');
    Route::get('/supplier-groups/{supplierGroup}/edit',[SupplierGroupController::class,'edit'])->middleware('permission:supplier_groups.edit')->name('supplier-groups.edit');
    Route::put('/supplier-groups/{supplierGroup}',[SupplierGroupController::class,'update'])->middleware('permission:supplier_groups.edit')->name('supplier-groups.update');
    Route::delete('/supplier-groups/{supplierGroup}',[SupplierGroupController::class,'destroy'])->middleware('permission:supplier_groups.delete')->name('supplier-groups.destroy');
    Route::get('/suppliers',[SupplierController::class,'index'])->middleware('permission:suppliers.view')->name('suppliers.index');
    Route::get('/suppliers/create',[SupplierController::class,'create'])->middleware('permission:suppliers.create')->name('suppliers.create');
    Route::post('/suppliers',[SupplierController::class,'store'])->middleware('permission:suppliers.create')->name('suppliers.store');
    Route::get('/suppliers/{supplier}',[SupplierController::class,'show'])->middleware('permission:suppliers.view')->name('suppliers.show');
    Route::get('/suppliers/{supplier}/edit',[SupplierController::class,'edit'])->middleware('permission:suppliers.edit')->name('suppliers.edit');
    Route::put('/suppliers/{supplier}',[SupplierController::class,'update'])->middleware('permission:suppliers.edit')->name('suppliers.update');
    Route::delete('/suppliers/{supplier}',[SupplierController::class,'destroy'])->middleware('permission:suppliers.delete')->name('suppliers.destroy');
    Route::get('/suppliers/{supplier}/ledger',[SupplierLedgerController::class,'show'])->middleware('permission:suppliers.ledger')->name('suppliers.ledger');

    // Products
    foreach ([
        ['units',UnitController::class,'units'],['categories',CategoryController::class,'categories'],['brands',BrandController::class,'brands']
    ] as [$uri,$controller,$permission]) {
        Route::get("/$uri",[$controller,'index'])->middleware("permission:$permission.view")->name("$uri.index");
        Route::get("/$uri/create",[$controller,'create'])->middleware("permission:$permission.create")->name("$uri.create");
        Route::post("/$uri",[$controller,'store'])->middleware("permission:$permission.create")->name("$uri.store");
        $param=rtrim($uri,'s'); if($uri==='categories')$param='category';
        Route::get("/$uri/{".$param."}/edit",[$controller,'edit'])->middleware("permission:$permission.edit")->name("$uri.edit");
        Route::put("/$uri/{".$param."}",[$controller,'update'])->middleware("permission:$permission.edit")->name("$uri.update");
        Route::delete("/$uri/{".$param."}",[$controller,'destroy'])->middleware("permission:$permission.delete")->name("$uri.destroy");
    }
    Route::get('/products',[ProductController::class,'index'])->middleware('permission:products.view')->name('products.index');
    Route::get('/products/create',[ProductController::class,'create'])->middleware('permission:products.create')->name('products.create');
    Route::post('/products',[ProductController::class,'store'])->middleware('permission:products.create')->name('products.store');
    Route::get('/products/{product}',[ProductController::class,'show'])->middleware('permission:products.view')->name('products.show');
    Route::get('/products/{product}/edit',[ProductController::class,'edit'])->middleware('permission:products.edit')->name('products.edit');
    Route::put('/products/{product}',[ProductController::class,'update'])->middleware('permission:products.edit')->name('products.update');
    Route::delete('/products/{product}',[ProductController::class,'destroy'])->middleware('permission:products.delete')->name('products.destroy');
    Route::get('/products/{product}/variants',[ProductVariantController::class,'index'])->middleware('permission:products.view')->name('products.variants.index');
    Route::post('/products/{product}/variants',[ProductVariantController::class,'store'])->middleware('permission:products.create')->name('products.variants.store');
    Route::delete('/products/{product}/variants/{variant}',[ProductVariantController::class,'destroy'])->middleware('permission:products.delete')->name('products.variants.destroy');


    // Client Products module workflows
    Route::get('/product-setup',ProductSetupController::class)->middleware('permission:products.view')->name('product-setup.index');
    Route::get('/opening-stock',[OpeningStockController::class,'index'])->middleware('permission:inventory.opening')->name('opening-stock.index');
    Route::get('/opening-stock/create',[OpeningStockController::class,'create'])->middleware('permission:inventory.opening')->name('opening-stock.create');
    Route::post('/opening-stock',[OpeningStockController::class,'store'])->middleware('permission:inventory.opening')->name('opening-stock.store');
    Route::get('/opening-stock/{openingStock}',[OpeningStockController::class,'show'])->middleware('permission:inventory.opening')->name('opening-stock.show');
    Route::post('/opening-stock/{openingStock}/post',[OpeningStockController::class,'post'])->middleware('permission:inventory.opening')->name('opening-stock.post');
    Route::get('/product-returns',[ProductReturnHubController::class,'index'])->middleware('permission:sale_returns.view|purchase_returns.view')->name('product-returns.index');

    Route::get('/product-delivery-purchase',[ProductDeliveryController::class,'purchases'])->middleware('permission:purchases.view')->name('product-delivery-purchase.index');
    Route::get('/product-delivery-purchase/create',[ProductDeliveryController::class,'createPurchase'])->middleware('permission:purchases.post')->name('product-delivery-purchase.create');
    Route::post('/product-delivery-purchase',[ProductDeliveryController::class,'storePurchase'])->middleware('permission:purchases.post')->name('product-delivery-purchase.store');
    Route::get('/product-delivery-purchase/{delivery}',[ProductDeliveryController::class,'showPurchase'])->middleware('permission:purchases.view')->name('product-delivery-purchase.show');
    Route::post('/product-delivery-purchase/{delivery}/post',[ProductDeliveryController::class,'postPurchase'])->middleware('permission:purchases.post')->name('product-delivery-purchase.post');
    Route::delete('/product-delivery-purchase/{delivery}',[ProductDeliveryController::class,'destroyPurchase'])->middleware('permission:purchases.post')->name('product-delivery-purchase.destroy');
    Route::get('/product-delivery-purchase/{delivery}/print',[ProductDeliveryController::class,'printPurchase'])->middleware('permission:purchases.view')->name('product-delivery-purchase.print');

    Route::get('/product-delivery-sells',[ProductDeliveryController::class,'sales'])->middleware('permission:sales.view')->name('product-delivery-sales.index');
    Route::get('/product-delivery-sells/create',[ProductDeliveryController::class,'createSale'])->middleware('permission:sales.post')->name('product-delivery-sales.create');
    Route::post('/product-delivery-sells',[ProductDeliveryController::class,'storeSale'])->middleware('permission:sales.post')->name('product-delivery-sales.store');
    Route::get('/product-delivery-sells/{delivery}',[ProductDeliveryController::class,'showSale'])->middleware('permission:sales.view')->name('product-delivery-sales.show');
    Route::post('/product-delivery-sells/{delivery}/post',[ProductDeliveryController::class,'postSale'])->middleware('permission:sales.post')->name('product-delivery-sales.post');
    Route::delete('/product-delivery-sells/{delivery}',[ProductDeliveryController::class,'destroySale'])->middleware('permission:sales.post')->name('product-delivery-sales.destroy');
    Route::get('/product-delivery-sells/{delivery}/print',[ProductDeliveryController::class,'printSale'])->middleware('permission:sales.view')->name('product-delivery-sales.print');

    // Warehouse / Inventory
    Route::get('/warehouses',[WarehouseController::class,'index'])->middleware('permission:warehouses.view')->name('warehouses.index');
    Route::get('/warehouses/create',[WarehouseController::class,'create'])->middleware('permission:warehouses.create')->name('warehouses.create');
    Route::post('/warehouses',[WarehouseController::class,'store'])->middleware('permission:warehouses.create')->name('warehouses.store');
    Route::get('/warehouses/{warehouse}/edit',[WarehouseController::class,'edit'])->middleware('permission:warehouses.edit')->name('warehouses.edit');
    Route::put('/warehouses/{warehouse}',[WarehouseController::class,'update'])->middleware('permission:warehouses.edit')->name('warehouses.update');
    Route::delete('/warehouses/{warehouse}',[WarehouseController::class,'destroy'])->middleware('permission:warehouses.delete')->name('warehouses.destroy');
    Route::get('/inventory',[InventoryController::class,'index'])->middleware('permission:inventory.view')->name('inventory.index');
    Route::get('/inventory/live',[InventoryController::class,'live'])->middleware('permission:inventory.view')->name('inventory.live');
    Route::get('/inventory/reorder-suggestions',[InventoryController::class,'reorderSuggestions'])->middleware('permission:inventory.view')->name('inventory.reorder-suggestions');
    Route::get('/inventory/excess-stock',[InventoryController::class,'excessStock'])->middleware('permission:inventory.view')->name('inventory.excess-stock');
    Route::get('/inventory/damaged-goods',[InventoryController::class,'damagedGoods'])->middleware('permission:inventory.view')->name('inventory.damaged-goods');
    Route::get('/inventory/batches',[InventoryController::class,'batches'])->middleware('permission:inventory.view')->name('inventory.batches');
    Route::post('/inventory/batches',[InventoryController::class,'storeBatch'])->middleware('permission:inventory.adjust')->name('inventory.batches.store');
    Route::get('/inventory/expiry',[InventoryController::class,'expiry'])->middleware('permission:inventory.view')->name('inventory.expiry');
    Route::get('/inventory/valuation',[InventoryController::class,'valuation'])->middleware('permission:inventory.view')->name('inventory.valuation');
    Route::get('/inventory/damaged-goods/create',[StockAdjustmentController::class,'createDamage'])->middleware('permission:inventory.adjust')->name('inventory.damaged-goods.create');
    Route::get('/inventory/ledger',[StockLedgerController::class,'index'])->middleware('permission:inventory.ledger')->name('inventory.ledger');
    Route::resource('stock-adjustments',StockAdjustmentController::class)->middleware('permission:inventory.adjust');
    Route::post('/stock-adjustments/{stockAdjustment}/post',[StockAdjustmentController::class,'post'])->middleware('permission:inventory.adjust')->name('stock-adjustments.post');
    Route::resource('stock-transfers',StockTransferController::class)->middleware('permission:inventory.transfer');
    Route::post('/stock-transfers/{stockTransfer}/post',[StockTransferController::class,'post'])->middleware('permission:inventory.transfer')->name('stock-transfers.post');

    // Purchases
    Route::get('/purchases',[PurchaseController::class,'index'])->middleware('permission:purchases.view')->name('purchases.index');
    Route::get('/purchases/create',[PurchaseController::class,'create'])->middleware('permission:purchases.create')->name('purchases.create');
    Route::post('/purchases',[PurchaseController::class,'store'])->middleware('permission:purchases.create')->name('purchases.store');
    Route::get('/purchases/{purchase}',[PurchaseController::class,'show'])->middleware('permission:purchases.view')->name('purchases.show');
    Route::get('/purchases/{purchase}/edit',[PurchaseController::class,'edit'])->middleware('permission:purchases.edit')->name('purchases.edit');
    Route::put('/purchases/{purchase}',[PurchaseController::class,'update'])->middleware('permission:purchases.edit')->name('purchases.update');
    Route::delete('/purchases/{purchase}',[PurchaseController::class,'destroy'])->middleware('permission:purchases.delete')->name('purchases.destroy');
    Route::post('/purchases/{purchase}/post',[PurchaseController::class,'post'])->middleware('permission:purchases.post')->name('purchases.post');
    Route::get('/purchases/{purchase}/invoice',[PurchaseController::class,'invoice'])->middleware('permission:purchases.view')->name('purchases.invoice');
    Route::get('/purchase-returns',[PurchaseReturnController::class,'index'])->middleware('permission:purchase_returns.view')->name('purchase-returns.index');
    Route::get('/purchase-returns/create',[PurchaseReturnController::class,'create'])->middleware('permission:purchase_returns.create')->name('purchase-returns.create');
    Route::post('/purchase-returns',[PurchaseReturnController::class,'store'])->middleware('permission:purchase_returns.create')->name('purchase-returns.store');
    Route::get('/purchase-returns/{purchaseReturn}/edit',[PurchaseReturnController::class,'edit'])->middleware('permission:purchase_returns.edit')->name('purchase-returns.edit');
    Route::put('/purchase-returns/{purchaseReturn}',[PurchaseReturnController::class,'update'])->middleware('permission:purchase_returns.edit')->name('purchase-returns.update');
    Route::get('/purchase-returns/{purchaseReturn}',[PurchaseReturnController::class,'show'])->middleware('permission:purchase_returns.view')->name('purchase-returns.show');
    Route::delete('/purchase-returns/{purchaseReturn}',[PurchaseReturnController::class,'destroy'])->middleware('permission:purchase_returns.delete')->name('purchase-returns.destroy');
    Route::post('/purchase-returns/{purchaseReturn}/post',[PurchaseReturnController::class,'post'])->middleware('permission:purchase_returns.post')->name('purchase-returns.post');

    // Sales & quotations
    Route::get('/sales',[SaleController::class,'index'])->middleware('permission:sales.view')->name('sales.index');
    Route::get('/sales/create',[SaleController::class,'create'])->middleware('permission:sales.create')->name('sales.create');
    Route::post('/sales',[SaleController::class,'store'])->middleware('permission:sales.create')->name('sales.store');
    Route::get('/sales/{sale}/memo',[SaleController::class,'memo'])->middleware('permission:sales.view')->name('sales.memo');
    Route::get('/sales/{sale}/invoice',[SaleController::class,'memo'])->middleware('permission:sales.view')->name('sales.invoice');
    Route::get('/sales/{sale}/challan',[SaleController::class,'challan'])->middleware('permission:sales.view')->name('sales.challan');
    Route::get('/sales/{sale}',[SaleController::class,'show'])->middleware('permission:sales.view')->name('sales.show');
    Route::get('/sales/{sale}/edit',[SaleController::class,'edit'])->middleware('permission:sales.edit')->name('sales.edit');
    Route::put('/sales/{sale}',[SaleController::class,'update'])->middleware('permission:sales.edit')->name('sales.update');
    Route::delete('/sales/{sale}',[SaleController::class,'destroy'])->middleware('permission:sales.delete')->name('sales.destroy');
    Route::post('/sales/{sale}/post',[SaleController::class,'post'])->middleware('permission:sales.post')->name('sales.post');
    Route::get('/pos',[PosController::class,'index'])->middleware(['permission:sales.create','permission:sales.post','permission:sales.view'])->name('pos.index');
    Route::post('/pos',[PosController::class,'store'])->middleware(['permission:sales.create','permission:sales.post','permission:sales.view'])->name('pos.store');
    Route::get('/sale-returns',[SaleReturnController::class,'index'])->middleware('permission:sale_returns.view')->name('sale-returns.index');
    Route::get('/sale-returns/create',[SaleReturnController::class,'create'])->middleware('permission:sale_returns.create')->name('sale-returns.create');
    Route::post('/sale-returns',[SaleReturnController::class,'store'])->middleware('permission:sale_returns.create')->name('sale-returns.store');
    Route::get('/sale-returns/{saleReturn}/edit',[SaleReturnController::class,'edit'])->middleware('permission:sale_returns.edit')->name('sale-returns.edit');
    Route::put('/sale-returns/{saleReturn}',[SaleReturnController::class,'update'])->middleware('permission:sale_returns.edit')->name('sale-returns.update');
    Route::get('/sale-returns/{saleReturn}',[SaleReturnController::class,'show'])->middleware('permission:sale_returns.view')->name('sale-returns.show');
    Route::delete('/sale-returns/{saleReturn}',[SaleReturnController::class,'destroy'])->middleware('permission:sale_returns.delete')->name('sale-returns.destroy');
    Route::post('/sale-returns/{saleReturn}/post',[SaleReturnController::class,'post'])->middleware('permission:sale_returns.post')->name('sale-returns.post');
    Route::get('/quotations',[QuotationController::class,'index'])->middleware('permission:quotations.view')->name('quotations.index');
    Route::get('/quotations/create',[QuotationController::class,'create'])->middleware('permission:quotations.create')->name('quotations.create');
    Route::post('/quotations',[QuotationController::class,'store'])->middleware('permission:quotations.create')->name('quotations.store');
    Route::get('/quotations/{quotation}/edit',[QuotationController::class,'edit'])->middleware('permission:quotations.edit')->name('quotations.edit');
    Route::put('/quotations/{quotation}',[QuotationController::class,'update'])->middleware('permission:quotations.edit')->name('quotations.update');
    Route::get('/quotations/{quotation}',[QuotationController::class,'show'])->middleware('permission:quotations.view')->name('quotations.show');
    Route::delete('/quotations/{quotation}',[QuotationController::class,'destroy'])->middleware('permission:quotations.delete')->name('quotations.destroy');
    Route::patch('/quotations/{quotation}/status',[QuotationController::class,'status'])->middleware('permission:quotations.edit')->name('quotations.status');

    // Payments
    Route::get('/customer-payments',[CustomerPaymentController::class,'index'])->middleware('permission:customer_payments.view')->name('customer-payments.index');
    Route::get('/customer-payments/create',[CustomerPaymentController::class,'create'])->middleware('permission:customer_payments.create')->name('customer-payments.create');
    Route::post('/customer-payments',[CustomerPaymentController::class,'store'])->middleware('permission:customer_payments.create')->name('customer-payments.store');
    Route::get('/supplier-payments',[SupplierPaymentController::class,'index'])->middleware('permission:supplier_payments.view')->name('supplier-payments.index');
    Route::get('/supplier-payments/create',[SupplierPaymentController::class,'create'])->middleware('permission:supplier_payments.create')->name('supplier-payments.create');
    Route::post('/supplier-payments',[SupplierPaymentController::class,'store'])->middleware('permission:supplier_payments.create')->name('supplier-payments.store');

    // Expenses
    Route::get('/expense-categories',[ExpenseCategoryController::class,'index'])->middleware('permission:expense_categories.view')->name('expense-categories.index');
    Route::get('/expense-categories/create',[ExpenseCategoryController::class,'create'])->middleware('permission:expense_categories.create')->name('expense-categories.create');
    Route::post('/expense-categories',[ExpenseCategoryController::class,'store'])->middleware('permission:expense_categories.create')->name('expense-categories.store');
    Route::get('/expense-categories/{expenseCategory}/edit',[ExpenseCategoryController::class,'edit'])->middleware('permission:expense_categories.edit')->name('expense-categories.edit');
    Route::put('/expense-categories/{expenseCategory}',[ExpenseCategoryController::class,'update'])->middleware('permission:expense_categories.edit')->name('expense-categories.update');
    Route::delete('/expense-categories/{expenseCategory}',[ExpenseCategoryController::class,'destroy'])->middleware('permission:expense_categories.delete')->name('expense-categories.destroy');
    Route::get('/expenses',[ExpenseController::class,'index'])->middleware('permission:expenses.view')->name('expenses.index');
    Route::get('/expenses/create',[ExpenseController::class,'create'])->middleware('permission:expenses.create')->name('expenses.create');
    Route::post('/expenses',[ExpenseController::class,'store'])->middleware('permission:expenses.create')->name('expenses.store');
    Route::get('/expenses/{expense}/edit',[ExpenseController::class,'edit'])->middleware('permission:expenses.edit')->name('expenses.edit');
    Route::put('/expenses/{expense}',[ExpenseController::class,'update'])->middleware('permission:expenses.edit')->name('expenses.update');
    Route::get('/expenses/{expense}',[ExpenseController::class,'show'])->middleware('permission:expenses.view')->name('expenses.show');
    Route::delete('/expenses/{expense}',[ExpenseController::class,'destroy'])->middleware('permission:expenses.delete')->name('expenses.destroy');
    Route::post('/expenses/{expense}/post',[ExpenseController::class,'post'])->middleware('permission:expenses.post')->name('expenses.post');

    // Payroll
    Route::get('/employees',[EmployeeController::class,'index'])->middleware('permission:employees.view')->name('employees.index');
    Route::get('/employees/create',[EmployeeController::class,'create'])->middleware('permission:employees.create')->name('employees.create');
    Route::post('/employees',[EmployeeController::class,'store'])->middleware('permission:employees.create')->name('employees.store');
    Route::get('/employees/{employee}/edit',[EmployeeController::class,'edit'])->middleware('permission:employees.edit')->name('employees.edit');
    Route::put('/employees/{employee}',[EmployeeController::class,'update'])->middleware('permission:employees.edit')->name('employees.update');
    Route::delete('/employees/{employee}',[EmployeeController::class,'destroy'])->middleware('permission:employees.delete')->name('employees.destroy');
    Route::get('/payrolls',[PayrollController::class,'index'])->middleware('permission:payroll.view')->name('payrolls.index');
    Route::get('/payrolls/create',[PayrollController::class,'create'])->middleware('permission:payroll.create')->name('payrolls.create');
    Route::post('/payrolls',[PayrollController::class,'store'])->middleware('permission:payroll.create')->name('payrolls.store');
    Route::get('/payrolls/{payroll}',[PayrollController::class,'show'])->middleware('permission:payroll.view')->name('payrolls.show');
    Route::put('/payrolls/{payroll}',[PayrollController::class,'update'])->middleware('permission:payroll.edit')->name('payrolls.update');
    Route::delete('/payrolls/{payroll}',[PayrollController::class,'destroy'])->middleware('permission:payroll.delete')->name('payrolls.destroy');
    Route::post('/payrolls/{payroll}/post',[PayrollController::class,'post'])->middleware('permission:payroll.post')->name('payrolls.post');
    Route::get('/payroll-payments',[PayrollPaymentController::class,'index'])->middleware('permission:payroll.pay')->name('payroll-payments.index');
    Route::get('/payroll-payments/create',[PayrollPaymentController::class,'create'])->middleware('permission:payroll.pay')->name('payroll-payments.create');
    Route::post('/payroll-payments',[PayrollPaymentController::class,'store'])->middleware('permission:payroll.pay')->name('payroll-payments.store');

    // Accounting
    Route::get('/accounts',[AccountController::class,'index'])->middleware('permission:accounts.view')->name('accounts.index');
    Route::get('/accounts/create',[AccountController::class,'create'])->middleware('permission:accounts.create')->name('accounts.create');
    Route::post('/accounts',[AccountController::class,'store'])->middleware('permission:accounts.create')->name('accounts.store');
    Route::get('/accounts/{account}/edit',[AccountController::class,'edit'])->middleware('permission:accounts.edit')->name('accounts.edit');
    Route::put('/accounts/{account}',[AccountController::class,'update'])->middleware('permission:accounts.edit')->name('accounts.update');
    Route::delete('/accounts/{account}',[AccountController::class,'destroy'])->middleware('permission:accounts.delete')->name('accounts.destroy');
    Route::get('/journals',[JournalController::class,'index'])->middleware('permission:journals.view')->name('journals.index');
    Route::get('/journals/create',[JournalController::class,'create'])->middleware('permission:journals.create')->name('journals.create');
    Route::post('/journals',[JournalController::class,'store'])->middleware('permission:journals.create')->name('journals.store');
    Route::get('/journals/{journal}',[JournalController::class,'show'])->middleware('permission:journals.view')->name('journals.show');

    // Money Transactions (business-facing Cash/Bank receive, payment and transfer)
    Route::get('/money-transactions',[MoneyTransactionController::class,'index'])->middleware('permission:journals.view')->name('money-transactions.index');
    Route::get('/money-transactions/export',[MoneyTransactionController::class,'export'])->middleware('permission:journals.view')->name('money-transactions.export');
    Route::get('/money-transactions/create',[MoneyTransactionController::class,'create'])->middleware('permission:journals.create')->name('money-transactions.create');
    Route::post('/money-transactions',[MoneyTransactionController::class,'store'])->middleware('permission:journals.create')->name('money-transactions.store');
    Route::get('/money-transactions/{journal}/print',[MoneyTransactionController::class,'print'])->middleware('permission:journals.view')->name('money-transactions.print');

    // Loan Management
    Route::get('/loans',[LoanController::class,'index'])->middleware('permission:accounts.view')->name('loans.index');
    Route::get('/loans/create',[LoanController::class,'create'])->middleware('permission:accounts.create')->name('loans.create');
    Route::post('/loans',[LoanController::class,'store'])->middleware('permission:accounts.create')->name('loans.store');
    Route::get('/loans/{loan}',[LoanController::class,'show'])->middleware('permission:accounts.view')->name('loans.show');
    Route::post('/loans/{loan}/payments',[LoanController::class,'payment'])->middleware('permission:accounts.create')->name('loans.payments.store');

    // Reports
    Route::prefix('reports')->middleware('permission:reports.view')->name('reports.')->group(function(){
        Route::get('/',[ReportController::class,'index'])->name('index');
        Route::get('/profit-loss',[ReportController::class,'profitLoss'])->name('profit-loss');
        Route::get('/trial-balance',[ReportController::class,'trialBalance'])->name('trial-balance');
        Route::get('/balance-sheet',[ReportController::class,'balanceSheet'])->name('balance-sheet');
        Route::get('/cash-flow',[ReportController::class,'cashFlow'])->name('cash-flow');
        Route::get('/cashbook',[ReportController::class,'cashbook'])->name('cashbook');
        Route::get('/general-ledger',[ReportController::class,'generalLedger'])->name('general-ledger');
        Route::get('/financial',[ReportController::class,'financial'])->name('financial');
        Route::get('/client-ledger',[ReportController::class,'clientLedger'])->name('client-ledger');
        Route::get('/transactions-package',[ReportController::class,'transactionsPackage'])->name('transactions-package');
        Route::get('/receivable-payable',[ReportController::class,'receivablePayable'])->name('receivable-payable');
        Route::get('/gap',[ReportController::class,'gapReport'])->name('gap');
        Route::get('/top-sheet',[ReportController::class,'topSheet'])->name('top-sheet');
        Route::get('/daily-performance',[ReportController::class,'dailyPerformance'])->name('daily-performance');
        Route::get('/vouchers',[ReportController::class,'vouchers'])->name('vouchers');
        Route::get('/vouchers/{journal}/print',[ReportController::class,'voucherPrint'])->name('vouchers.print');
        Route::get('/sales',[ReportController::class,'sales'])->name('sales');
        Route::get('/purchases',[ReportController::class,'purchases'])->name('purchases');
        Route::get('/customers',[ReportController::class,'customers'])->name('customers');
        Route::get('/suppliers',[ReportController::class,'suppliers'])->name('suppliers');
        Route::get('/products',[ReportController::class,'products'])->name('products');
        Route::get('/payments',[ReportController::class,'payments'])->name('payments');
        Route::get('/expenses',[ReportController::class,'expenses'])->name('expenses');
        Route::get('/income-expense',[ReportController::class,'incomeExpense'])->name('income-expense');
        Route::get('/registers',[ReportController::class,'registers'])->name('registers');
    });

    // System
    Route::get('/settings/client-setup',[ClientSetupController::class,'index'])->middleware('permission:client_setup.view')->name('client-setup.index');
    Route::get('/settings/reorder-levels',[ReorderLevelController::class,'index'])->middleware('permission:reorder_setup.view')->name('reorder-levels.index');
    Route::put('/settings/reorder-levels',[ReorderLevelController::class,'update'])->middleware('permission:reorder_setup.edit')->name('reorder-levels.update');

    Route::get('/settings/business-days',[BusinessDayController::class,'index'])->middleware('permission:business_days.view')->name('business-days.index');
    Route::post('/settings/business-days/open',[BusinessDayController::class,'open'])->middleware('permission:business_days.manage')->name('business-days.open');
    Route::post('/settings/business-days/{businessDay}/close',[BusinessDayController::class,'close'])->middleware('permission:business_days.manage')->name('business-days.close');
    Route::post('/settings/business-days/{businessDay}/reopen',[BusinessDayController::class,'reopen'])->middleware('permission:business_days.manage')->name('business-days.reopen');

    Route::get('/settings/user-modules',[UserModuleController::class,'index'])->middleware('permission:user_modules.view')->name('user-modules.index');
    Route::get('/settings/user-modules/{user}/edit',[UserModuleController::class,'edit'])->middleware('permission:user_modules.view')->name('user-modules.edit');
    Route::put('/settings/user-modules/{user}',[UserModuleController::class,'update'])->middleware('permission:user_modules.edit')->name('user-modules.update');

    Route::get('/settings/market-hierarchy',[MarketHierarchyController::class,'index'])->middleware('permission:market_hierarchy.view')->name('market-hierarchy.index');
    Route::post('/settings/market-hierarchy',[MarketHierarchyController::class,'store'])->middleware('permission:market_hierarchy.create')->name('market-hierarchy.store');
    Route::put('/settings/market-hierarchy/{marketHierarchy}',[MarketHierarchyController::class,'update'])->middleware('permission:market_hierarchy.edit')->name('market-hierarchy.update');
    Route::delete('/settings/market-hierarchy/{marketHierarchy}',[MarketHierarchyController::class,'destroy'])->middleware('permission:market_hierarchy.delete')->name('market-hierarchy.destroy');

    Route::get('/settings/barcode-print',[BarcodePrintController::class,'index'])->middleware('permission:barcode_print.view')->name('barcode-print.index');
    Route::post('/settings/barcode-print/preview',[BarcodePrintController::class,'preview'])->middleware('permission:barcode_print.view')->name('barcode-print.preview');



    // Production-grade ERP controls
    Route::prefix('production-controls')->name('production-controls.')->group(function(){
        Route::get('/',[ProductionControlController::class,'hub'])->middleware('permission:production_controls.view')->name('hub');
        Route::get('/periods',[ProductionControlController::class,'periods'])->middleware('permission:production_controls.view')->name('periods');
        Route::post('/periods',[ProductionControlController::class,'periodStore'])->middleware('permission:production_controls.manage')->name('periods.store');
        Route::post('/periods/{id}/close',[ProductionControlController::class,'periodClose'])->middleware('permission:production_controls.manage')->name('periods.close');
        Route::post('/periods/{id}/reopen',[ProductionControlController::class,'periodReopen'])->middleware('permission:production_controls.manage')->name('periods.reopen');
        Route::get('/bank-reconciliation',[ProductionControlController::class,'bank'])->middleware('permission:bank_reconciliation.view')->name('bank');
        Route::post('/bank-reconciliation',[ProductionControlController::class,'bankStore'])->middleware('permission:bank_reconciliation.manage')->name('bank.store');
        Route::post('/bank-reconciliation/draft/reset',[ProductionControlController::class,'bankResetDraft'])->middleware('permission:bank_reconciliation.manage')->name('bank.draft.reset');
        Route::get('/taxes',[ProductionControlController::class,'taxes'])->middleware('permission:taxes.view')->name('taxes');
        Route::post('/taxes',[ProductionControlController::class,'taxStore'])->middleware('permission:taxes.manage')->name('taxes.store');
        Route::post('/taxes/assign',[ProductionControlController::class,'taxAssign'])->middleware('permission:taxes.manage')->name('taxes.assign');
        Route::get('/approvals',[ProductionControlController::class,'approvals'])->middleware('permission:approvals.view')->name('approvals');
        Route::post('/approvals/toggle',[ProductionControlController::class,'approvalToggle'])->middleware('permission:approvals.manage')->name('approvals.toggle');
        Route::post('/approvals/{id}/decide',[ProductionControlController::class,'approvalDecide'])->middleware('permission:approvals.manage')->name('approvals.decide');
        Route::get('/orders/{type}',[ProductionControlController::class,'orders'])->middleware('permission:orders.view')->name('orders');
        Route::post('/orders/{type}',[ProductionControlController::class,'orderStore'])->middleware('permission:orders.manage')->name('orders.store');
        Route::post('/orders/{type}/{id}/convert',[ProductionControlController::class,'orderConvert'])->middleware('permission:orders.manage')->name('orders.convert');
        Route::get('/reservations',[ProductionControlController::class,'reservations'])->middleware('permission:production_controls.view')->name('reservations');
        Route::post('/reservations',[ProductionControlController::class,'reservationStore'])->middleware('permission:production_controls.manage')->name('reservations.store');
        Route::post('/reservations/{id}/release',[ProductionControlController::class,'reservationRelease'])->middleware('permission:production_controls.manage')->name('reservations.release');
        Route::get('/serials',[ProductionControlController::class,'serials'])->middleware('permission:production_controls.view')->name('serials');
        Route::post('/serials',[ProductionControlController::class,'serialStore'])->middleware('permission:production_controls.manage')->name('serials.store');
        Route::put('/serials/{id}',[ProductionControlController::class,'serialUpdate'])->middleware('permission:production_controls.manage')->name('serials.update');
        Route::get('/currencies',[ProductionControlController::class,'currencies'])->middleware('permission:production_controls.view')->name('currencies');
        Route::post('/currencies',[ProductionControlController::class,'currencyStore'])->middleware('permission:production_controls.manage')->name('currencies.store');
        Route::post('/exchange-rates',[ProductionControlController::class,'rateStore'])->middleware('permission:production_controls.manage')->name('rates.store');
        Route::get('/attachments',[ProductionControlController::class,'attachments'])->middleware('permission:production_controls.view')->name('attachments');
        Route::post('/attachments',[ProductionControlController::class,'attachmentStore'])->middleware('permission:production_controls.manage')->name('attachments.store');
        Route::get('/attachments/{id}/download',[ProductionControlController::class,'attachmentDownload'])->middleware('permission:production_controls.view')->name('attachments.download');
        Route::get('/assets',[ProductionControlController::class,'assets'])->middleware('permission:assets.view')->name('assets');
        Route::post('/assets',[ProductionControlController::class,'assetStore'])->middleware('permission:assets.manage')->name('assets.store');
        Route::post('/assets/{id}/depreciate',[ProductionControlController::class,'assetDepreciate'])->middleware('permission:assets.manage')->name('assets.depreciate');
        Route::get('/planning',[ProductionControlController::class,'planning'])->middleware('permission:planning.view')->name('planning');
        Route::post('/planning/cost-centers',[ProductionControlController::class,'costCenterStore'])->middleware('permission:planning.manage')->name('cost-centers.store');
        Route::post('/planning/projects',[ProductionControlController::class,'projectStore'])->middleware('permission:planning.manage')->name('projects.store');
        Route::post('/planning/budgets',[ProductionControlController::class,'budgetStore'])->middleware('permission:planning.manage')->name('budgets.store');
    });

    Route::get('/settings',[SettingsController::class,'index'])->middleware('permission:settings.view')->name('settings.index');
    Route::put('/settings',[SettingsController::class,'update'])->middleware('permission:settings.edit')->name('settings.update');
    Route::get('/backups',[BackupController::class,'index'])->middleware('permission:settings.edit')->name('backups.index');
    Route::post('/backups',[BackupController::class,'store'])->middleware('permission:settings.edit')->name('backups.store');
    Route::get('/backups/{backup}/download',[BackupController::class,'download'])->middleware('permission:settings.edit')->name('backups.download');
    Route::post('/backups/{backup}/verify',[BackupController::class,'verify'])->middleware('permission:settings.edit')->name('backups.verify');
    Route::post('/backups/{backup}/verify-offsite',[BackupController::class,'verifyOffsite'])->middleware('permission:settings.edit')->name('backups.verify-offsite');
    Route::post('/backups/{backup}/restore',[BackupController::class,'restore'])->middleware('permission:settings.edit')->name('backups.restore');
    Route::delete('/backups/{backup}',[BackupController::class,'destroy'])->middleware('permission:settings.edit')->name('backups.destroy');
    Route::get('/audit-logs',[AuditLogController::class,'index'])->middleware('permission:audit.view')->name('audit-logs.index');
    Route::get('/notifications',[NotificationController::class,'index'])->middleware('permission:notifications.view')->name('notifications.index');
    Route::patch('/notifications/{notification}/read',[NotificationController::class,'read'])->middleware('permission:notifications.view')->name('notifications.read');
    Route::patch('/notifications/read-all',[NotificationController::class,'readAll'])->middleware('permission:notifications.view')->name('notifications.read-all');
});
