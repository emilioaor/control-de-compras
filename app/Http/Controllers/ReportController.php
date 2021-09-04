<?php

namespace App\Http\Controllers;

use App\Exports\ProductExport;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\PurchaseRequestGroup;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{

    /**
     * Product report
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function product()
    {
        return view('report.product');
    }

    /**
     * Product report data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function productData(Request $request)
    {
        $products = Product::query()->report($request->all())->get();

        return response()->json(['success' => true, 'data' => $products]);
    }

    /**
     * Product report download
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function productDownload(Request $request)
    {
        return Excel::download(new ProductExport($request->all()), 'products_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Product report
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function comparative()
    {
        return view('report.comparative');
    }

    /**
     * Comparative report data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function comparativeData(Request $request)
    {
        $response = ProductPrice::comparative();

        return response()->json(array_merge(['success' => true], $response));
    }

    /**
     * Order report
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function order()
    {
        return view('report.order');
    }

    /**
     * Report order
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderData(Request $request)
    {
        $orders = PurchaseRequestGroup::query()->report($request->all())->get();

        return response()->json(['success' => true, 'data' => $orders]);
    }
}
