<?php

namespace App\Models;

use App\Contract\SearchTrait;
use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Purchase extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UuidGeneratorTrait;
    use SearchTrait;

    protected $fillable = ['product_id', 'qty', 'purchase_group_id'];

    protected $search_fields= ['status', 'p.model', 'p.description'];

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
     * Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    /**
     * Search overwrite
     *
     * @param Builder $query
     * @param string|null $search
     * @return Builder
     * @throws \Exception
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        $query
            ->select(['purchases.*'])
            ->join('products', 'products.id', '=', 'purchases.product_id')
        ;

        return $this->_search($query, $search);
    }
}
