<?php

namespace App\Http\Controllers;

use App\Models\ModelNotFound;
use App\Models\PurchaseMovement;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestGroup;
use App\Models\PurchaseRequestHistory;
use App\Models\User;
use App\Service\AlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $purchaseRequests = PurchaseRequestGroup::query()
            ->search($request->search)
            ->my()
            ->latest()
            ->with(['seller', 'purchaseRequests.product', 'purchaseMovements'])
            ->paginate()
        ;

        return view('purchaseRequest.index', compact('purchaseRequests'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sellers = User::query()->where('role', User::ROLE_SELLER)->get();

        return view('purchaseRequest.form', compact('sellers'));
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

        $purchaseRequestGroup = PurchaseRequestGroup::query()
            ->thisWeek()
            ->bySeller($request->seller_id)
            ->with(['purchaseRequests'])
            ->firstOrCreate([], $request->all())
        ;

        foreach ($request->purchaseRequests as $current) {
            foreach ($current['products'] as $product) {

                if (! empty($product['qty'])) {

                    $purchaseRequest = $purchaseRequestGroup->purchaseRequests->where('product_id', $product['id'])->first();

                    if ($purchaseRequest) {
                        $purchaseRequest->qty += $product['qty'];
                        $purchaseRequest->important = $product['important'];
                    } else {

                        $purchaseRequest = new PurchaseRequest([
                            'purchase_request_group_id' => $purchaseRequestGroup->id,
                            'product_id' => $product['id'],
                            'qty' => $product['qty'],
                            'important' => $product['important'],
                            'note' => $product['note'],
                        ]);
                    }

                    $purchaseRequest->save();

                    $purchaseRequestHistory = new PurchaseRequestHistory();
                    $purchaseRequestHistory->purchase_request_id = $purchaseRequest->id;
                    $purchaseRequestHistory->qty = $product['qty'];
                    $purchaseRequestHistory->save();
                }
            }
        }

        DB::commit();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('purchase-request.index')]);
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
        $modelsNotFound = ModelNotFound::query()->thisWeek()->get();
        $inventory = PurchaseMovement::getInventoryAvailable();
        $purchaseRequestGroup = PurchaseRequestGroup::query()
            ->my()
            ->uuid($id)
            ->with(['purchaseRequests.product', 'purchaseMovements.product', 'seller'])
            ->firstOrFail()
        ;

        return view('purchaseRequest.edit', compact('purchaseRequestGroup', 'modelsNotFound', 'inventory'));
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
