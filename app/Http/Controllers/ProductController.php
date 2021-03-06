<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Service\AlertService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $products = Product::query()
            ->search($request->search)
            ->whereNotIn('id', $request->notIn ? explode(',', $request->notIn) : [])
            ->orderBy('description')
            ->with(['brand'])
            ->paginate()
        ;

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $products]);
        }

        return view('product.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $brands = Brand::query()->orderBy('name')->get();

        return view('product.form', compact('brands'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = new Product($request->all());
        $product->save();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('product.index')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::query()->upcOrSku($id, $id)->first();

        return response()->json(['success' => true, 'data' => $product]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::uuid($id)->firstOrFail();
        $brands = Brand::query()->orderBy('name')->get();

        return view('product.form', compact('product', 'brands'));
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
        $product = Product::uuid($id)->firstOrFail();
        $product->fill($request->all());
        $product->save();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('product.edit', [$id])]);
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

    /**
     * Check if user exists
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function exists(Request $request)
    {
        $product = Product::query()->where('upc', $request->upc)->first();

        return response()->json(['success' => true, 'data' => $product]);
    }

    /**
     * Get models
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function models(Request $request)
    {
        $models = Product::query()
            ->select('model')
            ->model($request->search)
            ->whereNotIn('model', $request->notIn ? explode(',', $request->notIn) : [])
            ->groupBy('model')
            ->paginate()
        ;

        return response()->json(['success' => true, 'data' => $models]);
    }

    /**
     * Get products by model
     *
     * @param $model
     * @return \Illuminate\Http\JsonResponse
     */
    public function byModel($model)
    {
        $products = Product::query()->where('model', $model)->get();

        return response()->json(['success' => true, 'data' => $products]);
    }
}
