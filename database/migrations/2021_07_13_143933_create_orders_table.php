<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id()->startingValue(200);
            $table->foreignId('customer_id')->constrained('customers', 'id')->cascadeOnDelete();
            $table->unsignedFloat('total_price');
            $table->unsignedBigInteger('total_quantity');
            $table->unsignedFloat('total_tax')->nullable();
            $table->enum('status', ['hold', 'pending', 'success', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
