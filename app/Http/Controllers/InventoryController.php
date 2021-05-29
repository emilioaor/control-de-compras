<?php

namespace App\Http\Controllers;

use App\Models\PurchaseMovement;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{

    /**
     * Inventory list
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
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
        $purchaseRequests = PurchaseRequestGroup::query()
            ->where('status', PurchaseRequestGroup::STATUS_PENDING)
            ->with(['purchaseRequests.product.sameModel', 'seller'])
            ->get()
        ;

        return view('inventory.distribution', compact('purchaseRequests', 'inventory'));
    }

    /**
     * Store distribution
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeDistribution(Request $request, $id)
    {
        DB::beginTransaction();

        $purchaseRequestGroup = PurchaseRequestGroup::query()->uuid($id)->firstOrFail();
        $purchaseRequestGroup->status = PurchaseRequestGroup::STATUS_PROCESSED;
        $purchaseRequestGroup->save();

        foreach ($request->products as $p) {
            if ($p['approved'] > 0) {

                $movement = new PurchaseMovement();
                $movement->purchase_request_group_id = $purchaseRequestGroup->id;
                $movement->qty = $p['approved'] * -1;
                $movement->product_id = $p['id'];
                $movement->save();
            }
        }

        DB::commit();

        return response()->json(['success' => true]);
    }
}
