<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_movements', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->foreignId('purchase_group_id')->nullable()->constrained('purchase_groups');
            $table->foreignId('purchase_request_group_id')->nullable()->constrained('purchase_request_groups');
            $table->foreignId('product_id')->constrained('products');
            $table->integer('qty');
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
        Schema::dropIfExists('purchase_movements');
    }
}
