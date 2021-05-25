<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseMovement;
use App\Models\User;
use App\Service\AlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $purchases = Purchase::query()
            ->search($request->search)
            ->my()
            ->latest()
            ->with(['product', 'buyer'])
            ->paginate()
        ;

        return view('purchase.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $buyers = User::query()->where('role', User::ROLE_BUYER)->get();

        return view('purchase.form', compact('buyers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        foreach ($request->purchaseRequests as $current) {
            foreach ($current['products'] as $product) {

                if ($product['qty'] > 0) {

                    $purchase = new Purchase([
                        'product_id' => $product['id'],
                        'buyer_id' => $current['buyer_id'],
                        'qty' => $product['qty']
                    ]);
                    $purchase->save();

                    $purchaseMovement = new PurchaseMovement();
                    $purchaseMovement->purchase_id = $purchase->id;
                    $purchaseMovement->qty = $product['qty'];
                    $purchaseMovement->save();
                }
            }
        }

        DB::commit();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('purchase.index')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
