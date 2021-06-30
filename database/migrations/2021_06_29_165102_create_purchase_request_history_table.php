<?php

use App\Models\PurchaseRequestHistory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseRequestHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_request_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')->constrained('purchase_requests');
            $table->integer('qty');
            $table->timestamps();
        });

        $purchaseRequests = \App\Models\PurchaseRequest::all();
        foreach ($purchaseRequests as $purchaseRequest) {

            $purchaseRequestHistory = new PurchaseRequestHistory();
            $purchaseRequestHistory->purchase_request_id = $purchaseRequest->id;
            $purchaseRequestHistory->qty = $purchaseRequest->qty;
            $purchaseRequestHistory->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_request_histories');
    }
}
