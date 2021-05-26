<?php

namespace App\Models;

use App\Contract\SearchTrait;
use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseMovement extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UuidGeneratorTrait;
    use SearchTrait;

    protected $fillable = ['purchase_id', 'purchase_request_id', 'qty'];

    protected $search_fields = ['qty'];

    /**
     * Purchase
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * Purchase request
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class)->withTrashed();
    }

    /**
     * getInventoryAvailable
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getInventoryAvailable()
    {
        return PurchaseMovement::query()
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
    }
}
