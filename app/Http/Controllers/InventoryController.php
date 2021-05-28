<?php

namespace App\Http\Controllers;

use App\Models\PurchaseMovement;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestGroup;
use Illuminate\Http\Request;

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
}
