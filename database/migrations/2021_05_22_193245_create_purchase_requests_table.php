<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->enum('status', ['pending', 'processed']);
            $table->foreignId('seller_id')->constrained('users');
            $table->foreignId('product_id')->constrained('products');
            $table->integer('qty');
            $table->integer('approved')->nullable();
            $table->integer('refused')->nullable();
            $table->dateTime('processed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_requests');
    }
}
