<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->string('gateway_name');
            $table->string('tracking_code')->nullable();
            $table->string('ref_num')->nullable();
            $table->string('card_number')->nullable();
            $table->string('order_amount')->nullable();
            $table->string('final_amount')->nullable();
            $table->string('token')->nullable();
            $table->boolean('is_paid')->default(0);
            $table->dateTime('reserved_transaction')->nullable();
            $table->dateTime('pay_time')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('status_code_response_gateway')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
