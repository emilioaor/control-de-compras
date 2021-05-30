<?php

namespace App\Models;

use App\Contract\SearchTrait;
use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseMovement extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UuidGeneratorTrait;
    use SearchTrait;

    protected $fillable = ['purchase_group_id', 'purchase_request_group_id', 'product_id', 'qty'];

    protected $search_fields = ['products.model', 'products.description', 'products.upc'];

    /**
     * Purchase group
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchaseGroup()
    {
        return $this->belongsTo(PurchaseGroup::class)->withTrashed();
    }

    /**
     * Purchase request group
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchaseRequestGroup()
    {
        return $this->belongsTo(PurchaseRequestGroup::class)->withTrashed();
    }

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
     * getInventoryAvailable
     *
     * @return \Illuminate\Database\Eloquent\Collection|array
     */
    public static function getInventoryAvailable()
    {
        $ordered = PurchaseRequest::query()
            ->selectRaw('
                SUM(qty) as qty,
                products.upc,
                products.description,
                products.id as product_id
            ')
            ->join('purchase_request_groups as prg', 'prg.id', '=', 'purchase_requests.purchase_request_group_id')
            ->join('products', 'products.id', '=', 'purchase_requests.product_id')
            ->where('prg.status', PurchaseRequestGroup::STATUS_PENDING)
            ->groupBy('product_id')
            ->get()
        ;

        $purchaseMovements = PurchaseMovement::query()
            ->selectRaw('
                SUM(purchase_movements.qty) as qty,
                products.upc,
                products.description,
                products.id as product_id
            ')
            ->join('products', 'products.id', '=', 'purchase_movements.product_id')
            ->groupBy('product_id')
            ->orderBy('products.description')
            ->get()
        ;

        $response = new Collection();
        foreach ($purchaseMovements as $purchaseMovement) {

            $o = $ordered->where('product_id', $purchaseMovement->product_id)->first();

            $purchaseMovement->ordered =  $o->qty ?? 0;
            $purchaseMovement->balance = $purchaseMovement->qty - $purchaseMovement->ordered;

            $response->push($purchaseMovement);
        }

        foreach ($ordered as $o) {
            $pm = $response->where('product_id', $o->product_id)->first();

            if (! $pm) {
                $o->ordered = $o->qty;
                $o->qty = 0;
                $o->balance = $o->ordered * -1;

                $response->push($o);
            }
        }

        $response = $response->filter(function ($current) {
            return $current->qty > 0 || $current->ordered > 0;
        });

        return $response;
    }
}
