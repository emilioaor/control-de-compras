<?php

namespace App\Models;

use App\Contract\WeekTrait;
use App\Exceptions\ProductPriceImportException;
use App\Imports\ProductPriceImport;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProductPrice extends Model
{
    use HasFactory;
    use WeekTrait;

    protected $fillable = ['supplier_id', 'product_id', 'price'];

    /**
     * Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    /**
     * Supplier
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class)->withTrashed();
    }

    /**
     * Load prices file
     *
     * @param $base64
     * @param $supplierId
     * @return array|bool[]
     */
    public static function loadFile($base64, $supplierId)
    {
        try {
            $supplier = Supplier::find($supplierId);

            $explode = explode(',', $base64);
            $filename = sprintf('%s-%s', 'product-price', ((string) time()));
            $format = 'xlsx';

            $path = sprintf('product-prices/%s/%s.%s', $supplierId, $filename, $format);
            Storage::disk('public')->put($path, base64_decode($explode[1]));

            Excel::import(new ProductPriceImport($supplier), storage_path('app/public/' . $path));
        } catch (ProductPriceImportException $ex) {
            return ['success' => false, 'errors' => $ex->getErrors(), 'line' => $ex->getFileLine()];
        }

        return ['success' => true];
    }

    /**
     * Update prices
     *
     * @param $prices
     */
    public static function updatePrices($prices)
    {
        foreach ($prices as $pp) {
            if (! empty($pp['price'])) {

                $productPrice = ProductPrice::query()
                    ->thisWeek()
                    ->where('supplier_id', $pp['supplier_id'])
                    ->where('product_id', $pp['product_id'])
                    ->firstOrNew([
                        'supplier_id' => $pp['supplier_id'],
                        'product_id' => $pp['product_id']
                    ])
                ;

                $productPrice->price = $pp['price'];
                $productPrice->save();
            }
        }
    }
}
