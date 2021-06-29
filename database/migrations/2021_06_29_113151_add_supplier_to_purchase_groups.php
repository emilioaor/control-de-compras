<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSupplierToPurchaseGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_groups', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_groups', function (Blueprint $table) {
            $table->dropConstrainedForeignId('supplier_id');
        });
    }
}
