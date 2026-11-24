<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("UPDATE supplier_invoices AS invoice
            INNER JOIN (
                SELECT audit.auditable_id, audit.user_id, audit.created_at
                FROM audit_logs AS audit
                INNER JOIN (
                    SELECT auditable_id, MAX(id) AS latest_id
                    FROM audit_logs
                    WHERE auditable_type = 'App\\\\Models\\\\SupplierInvoice'
                      AND to_status IN ('MATCHED', 'ON_HOLD', 'REJECTED')
                      AND user_id IS NOT NULL
                    GROUP BY auditable_id
                ) AS latest ON latest.latest_id = audit.id
            ) AS review ON review.auditable_id = invoice.id
            SET invoice.reviewed_by = COALESCE(invoice.reviewed_by, review.user_id),
                invoice.reviewed_at = COALESCE(invoice.reviewed_at, review.created_at)
            WHERE invoice.reviewed_by IS NULL");

        DB::statement("UPDATE supplier_invoices AS invoice
            INNER JOIN (
                SELECT allocation.supplier_invoice_id, payment.paid_by, payment.payment_date
                FROM vendor_payment_allocations AS allocation
                INNER JOIN vendor_payments AS payment ON payment.id = allocation.vendor_payment_id
                INNER JOIN (
                    SELECT supplier_invoice_id, MAX(id) AS latest_id
                    FROM vendor_payment_allocations
                    GROUP BY supplier_invoice_id
                ) AS latest ON latest.latest_id = allocation.id
            ) AS last_payment ON last_payment.supplier_invoice_id = invoice.id
            SET invoice.paid_by = COALESCE(invoice.paid_by, last_payment.paid_by),
                invoice.paid_at = CASE
                    WHEN invoice.status = 'PAID' THEN COALESCE(invoice.paid_at, CAST(last_payment.payment_date AS DATETIME))
                    ELSE invoice.paid_at
                END
            WHERE invoice.paid_by IS NULL");
    }

    public function down(): void
    {
        // Preserve invoice audit and payment attribution once backfilled.
    }
};
