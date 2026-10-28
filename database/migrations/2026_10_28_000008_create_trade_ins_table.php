<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trade_ins', function (Blueprint $table) {
            $table->id();
            $table->string('trade_in_code', 50)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('target_order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('phone_model', 100);
            $table->string('storage_capacity', 20);
            $table->integer('battery_health');
            $table->string('physical_condition', 50);
            $table->string('screen_condition', 50);
            $table->string('accessories_status', 50);
            $table->string('imei', 30)->nullable();
            $table->decimal('estimated_price', 12, 2);
            $table->decimal('final_price', 12, 2)->nullable();
            $table->enum('status', ['submitted', 'unit_received', 'inspected', 'approved', 'rejected', 'paid_out'])->default('submitted')->index();
            $table->string('customer_bank_name', 50)->nullable();
            $table->string('customer_account_number', 50)->nullable();
            $table->string('customer_account_holder', 100)->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trade_ins');
    }
};
