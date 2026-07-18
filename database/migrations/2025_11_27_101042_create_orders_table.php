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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_id')->nullable();
            $table->string('email')->nullable();
            $table->string('vin')->nullable();
            $table->string('report_type')->nullable();
            $table->string('report_key')->nullable();
            $table->string('currency')->nullable();
            $table->string('locale')->nullable();
            $table->string('signature')->nullable();
            $table->enum('status', ['pending payment', 'paid', 'processing', 'completed', 'failed', 'refund', 'expired', 'fraud'])->default('pending payment');
            $table->string('reason')->nullable();
            $table->string('reasonCode')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->decimal('total_price', 10, 2)->nullable();

            $table->enum('order_purpose', ['report', 'topup_balance', 'topup_report_balance'])->nullable();
            $table->integer('report_to_add')->default(0);
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            $table->string('payment_method')->nullable();
            $table->json('payment_data')->nullable();

            $table->index('invoice_id');
            $table->index('vin');
            $table->index('email');
            $table->index('status');
            $table->index('order_purpose');
            $table->index('user_id');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('total_price');
            $table->index('reason');
            $table->index('reasonCode');
            $table->index('ip_address');
            $table->index('user_agent');
            $table->index('payment_method');
            $table->index('signature');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
