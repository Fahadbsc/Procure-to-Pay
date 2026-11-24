<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, DashboardController, VendorController, ItemController, UnitOfMeasureController, CostCentreController, PurchaseRequisitionController, PurchaseOrderController, GoodsReceiptController, SupplierInvoiceController, ReportController, BudgetController, ApprovalRuleController, ApprovalWorkflowController, VendorPaymentController, NotificationController, UserController};

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::get('/my-approvals', [ApprovalWorkflowController::class, 'index'])->name('my-approvals.index');
    Route::post('/my-approvals/{approvalRequest}/approve', [ApprovalWorkflowController::class, 'approve'])->name('my-approvals.approve');
    Route::post('/my-approvals/{approvalRequest}/reject', [ApprovalWorkflowController::class, 'reject'])->name('my-approvals.reject');

    Route::middleware('role:Admin,Requester')->group(function () {
        Route::resource('purchase-requisitions', PurchaseRequisitionController::class)->only(['index', 'create', 'store', 'show']);
        Route::post('purchase-requisitions/{purchaseRequisition}/submit', [PurchaseRequisitionController::class, 'submit'])->name('purchase-requisitions.submit');
    });

    Route::middleware('role:Admin,Procurement Officer')->group(function () {
        Route::resource('purchase-orders', PurchaseOrderController::class)->only(['index', 'create', 'store', 'show']);
        Route::post('purchase-orders/{purchaseOrder}/send', [PurchaseOrderController::class, 'send'])->name('purchase-orders.send');
        Route::post('purchase-orders/{purchaseOrder}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchase-orders.cancel');
        Route::resource('vendors', VendorController::class);
    });

    Route::middleware('role:Admin,Store Keeper')->group(function () {
        Route::resource('goods-receipts', GoodsReceiptController::class)->only(['index', 'create', 'store', 'show']);
    });

    Route::middleware('role:Admin,Accounts')->group(function () {
        Route::resource('supplier-invoices', SupplierInvoiceController::class)->only(['index', 'create', 'store', 'show']);
        foreach (['submit', 'reject', 'post', 'match', 'override', 'reject-on-hold'] as $action) {
            Route::post("supplier-invoices/{supplierInvoice}/{$action}", [SupplierInvoiceController::class, $action === 'mark-paid' ? 'markPaid' : ($action === 'reject-on-hold' ? 'rejectOnHold' : $action)])->name("supplier-invoices.{$action}");
        }
        Route::get('/payments', [VendorPaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/create', [VendorPaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [VendorPaymentController::class, 'store'])->name('payments.store');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });

    Route::middleware('role:Admin')->group(function () {
        Route::resource('users', UserController::class)->only(['index','create','store','edit','update']);
        Route::resource('items', ItemController::class);
        Route::resource('units', UnitOfMeasureController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::resource('cost-centres', CostCentreController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::get('/budgets', [BudgetController::class, 'index'])->name('budgets.index');
        Route::post('/budgets', [BudgetController::class, 'store'])->name('budgets.store');
        Route::resource('approval-rules', ApprovalRuleController::class)->except(['show']);
    });
});

Route::get('/', fn () => auth()->check() ? redirect()->route('dashboard') : redirect()->route('login'));
