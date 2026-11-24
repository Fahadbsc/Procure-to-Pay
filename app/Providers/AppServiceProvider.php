<?php

namespace App\Providers;

use App\Models\PurchaseRequisition;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceipt;
use App\Models\SupplierInvoice;
use App\Observers\StatusAuditObserver;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        PurchaseRequisition::observe(StatusAuditObserver::class);
        PurchaseOrder::observe(StatusAuditObserver::class);
        GoodsReceipt::observe(StatusAuditObserver::class);
        SupplierInvoice::observe(StatusAuditObserver::class);
    }
}
