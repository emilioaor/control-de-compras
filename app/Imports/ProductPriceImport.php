<?php

namespace App\Imports;

use App\Exceptions\ProductPriceImportException;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\ProductPrice;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductPriceImport implements ToCollection, WithHeadingRow
{

    protected $supplier;

    /**
     * ProductPriceImport constructor.
     * @param Supplier $supplier
     */
    public function __construct(Supplier $supplier)
    {
        $this->supplier = $supplier;
    }

    /**
     * @param Collection $collection
     * @throws ProductPriceImportException
     */
    public function collection(Collection $collection)
    {
        $rows = $this->getValidRows($collection);

        foreach ($rows as $row) {

            $productPrice = ProductPrice::query()
                ->thisWeek()
                ->where('supplier_id', $this->supplier->id)
                ->where('product_id', $row['product']->id)
                ->firstOrNew([
                    'supplier_id' => $this->supplier->id,
                    'product_id' => $row['product']->id
                ])
            ;

            $productPrice->price = $row['price'];
            $productPrice->save();
        }
    }

    /**
     * Is row valid?
     *
     * @param array $row
     * @param $index
     * @return bool
     * @throws ProductPriceImportException
     */
    public function isRowValid(array $row, $index)
    {
        if (isset($row['upc']) || isset($row['price'])) {

            $validator = Validator::make($row, [
                'upc' => 'required',
                'price' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                throw new ProductPriceImportException($validator->errors()->all(), $index + 2);
            }

            return true;
        }

        return false;
    }

    /**
     * Get valid rows
     *
     * @param Collection $collection
     * @return array
     * @throws ProductPriceImportException
     */
    public function getValidRows(Collection $collection)
    {
        $rows = [];

        foreach ($collection as $i => $row) {
            if ($this->isRowValid($row->toArray(), $i)) {

                $product = Product::query()->where('upc', $row['upc'])->first();

                if (! $product) {
                    throw new ProductPriceImportException(
                        [__('validation.exists', [
                            'attribute' => sprintf('UPC(%s)', $row['upc'])
                        ])],
                        $i + 2
                    );
                }

                $rows[] = array_merge($row->toArray(), compact('product'));
            }
        }

        if (count($rows) === 0) {
            throw new ProductPriceImportException(
                [__('validation.min.numeric', ['attribute' => 'products', 'min' => 1])],
                1
            );
        }

        return $rows;
    }
}
