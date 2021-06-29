<?php

namespace App\Http\Controllers;

use App\Models\ProductPrice;
use App\Models\Supplier;
use App\Service\AlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('productPrice.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        if ($request->type === 'file') {

            $load = ProductPrice::loadFile($request->file, $request->supplier_id);

            if (! $load['success']) {
                return response()->json(['success' => false, 'errors' => $load['errors'], 'line' => $load['line']], 400);
            }

        } elseif ($request->type === 'by_product') {
            ProductPrice::updatePrices($request->models);
        }

        DB::commit();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true]);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $supplier = Supplier::query()->uuid($id)->firstOrFail();
        $productPrices = ProductPrice::query()
            ->currentPrice()
            ->where('supplier_id', $supplier->id)
            ->with(['product'])
            ->get()
        ;

        return response()->json(['success' => true, 'data' => $productPrices]);
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
