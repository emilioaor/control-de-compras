<?php

namespace App\Http\Controllers;

use App\Models\ModelNotFound;
use App\Models\PurchaseMovement;
use App\Models\PurchaseRequestGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{

    /**
     * Inventory list
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $inventory = PurchaseMovement::getInventoryAvailable();

        return view('inventory.index', compact('inventory'));
    }

    /**
     * Inventory distribution
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function distribution()
    {
        $inventory = PurchaseMovement::getInventoryAvailable();
        $modelsNotFound = ModelNotFound::query()->thisWeek()->get();
        $purchaseRequests = PurchaseRequestGroup::query()
            ->thisWeek()
            ->with([
                'purchaseRequests.product.sameModel',
                'purchaseRequests.purchaseRequestHistories',
                'purchaseMovements',
                'seller'
            ])
            ->get()
        ;

        return view('inventory.distribution', compact('purchaseRequests', 'inventory', 'modelsNotFound'));
    }

    /**
     * Store distribution
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeDistribution(Request $request)
    {
        DB::beginTransaction();

        foreach ($request->products as $p) {
            foreach ($p['sellers'] as $s) {

                $purchaseRequestGroup = PurchaseRequestGroup::query()->thisWeek()->where('seller_id', $s['id'])->first();

                if (! empty($s['approved'])) {

                    $movement = new PurchaseMovement();
                    $movement->purchase_request_group_id = $purchaseRequestGroup->id;
                    $movement->qty = $s['approved'] * -1;
                    $movement->product_id = $p['id'];
                    $movement->save();
                }
            }
        }

        DB::commit();

        return response()->json(['success' => true]);
    }

    /**
     * Mark model as not found
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsNotFound(Request $request)
    {
        $modelNotFound = ModelNotFound::query()->where('model', $request->model)->thisWeek()->first();

        if ($modelNotFound){
            $modelNotFound->delete();
        } else {
            $modelNotFound = new ModelNotFound($request->all());
            $modelNotFound->save();
        }

        return response()->json(['success' => true]);
    }
}
