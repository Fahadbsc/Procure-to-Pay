<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
    public function up(): void { if(DB::getDriverName()==='mysql') DB::statement("ALTER TABLE purchase_requisitions MODIFY status ENUM('DRAFT','PENDING','SUBMITTED','APPROVED','REJECTED','PARTIALLY_CONVERTED','CONVERTED') NOT NULL DEFAULT 'DRAFT'"); }
    public function down(): void { if(DB::getDriverName()==='mysql'){DB::statement("UPDATE purchase_requisitions SET status='APPROVED' WHERE status='PARTIALLY_CONVERTED'");DB::statement("ALTER TABLE purchase_requisitions MODIFY status ENUM('DRAFT','PENDING','SUBMITTED','APPROVED','REJECTED','CONVERTED') NOT NULL DEFAULT 'DRAFT'");} }
};
