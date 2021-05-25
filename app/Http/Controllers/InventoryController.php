<?php

namespace App\Http\Controllers;

use App\Models\PurchaseMovement;
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
        $inventory = PurchaseMovement::query()
            ->selectRaw('
                SUM(purchase_movements.qty) as m_qty,
                COALESCE(ppr.upc, pp.upc) as upc,
                COALESCE(ppr.description, pp.description) as description,
                COALESCE(ppr.id, pp.id) as product_id
            ')
            ->leftJoin('purchases', 'purchases.id', '=', 'purchase_movements.purchase_id')
            ->leftJoin('purchase_requests', 'purchase_requests.id', '=', 'purchase_movements.purchase_request_id')
            ->leftJoin('products as ppr', 'ppr.id', '=', 'purchase_requests.product_id')
            ->leftJoin('products as pp', 'pp.id', '=', 'purchases.product_id')
            ->having('m_qty', '>', 0)
            ->groupBy('product_id')
            ->groupBy('upc')
            ->groupBy('description')
            ->get()
        ;

        return view('inventory.index', compact('inventory'));
    }
}
