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
            $table->foreignId('purchase_id')->nullable()->constrained('purchases');
            $table->foreignId('purchase_request_id')->nullable()->constrained('purchase_requests');
            $table->integer('qty');
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
        Schema::dropIfExists('purchase_movements');
    }
}
