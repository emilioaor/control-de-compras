<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseRequestGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_request_groups', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('number', 15)->unique();
            $table->foreignId('seller_id')->constrained('users');
            $table->boolean('excel_downloaded')->default(false);
            $table->string('customer_name');
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
        Schema::dropIfExists('purchase_request_groups');
    }
}
