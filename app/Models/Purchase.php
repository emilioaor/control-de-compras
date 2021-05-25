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

    protected $fillable = ['product_id', 'qty', 'buyer_id'];

    protected $search_fields= ['products.model', 'products.description'];

    /**
     * Purchase constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        if (! $this->id) {

            if (Auth::check() && Auth::user()->isBuyer()) {
                $attributes['buyer_id'] = Auth::user()->id;
            }
        }

        parent::__construct($attributes);
    }

    /**
     * Buyer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id')->withTrashed();
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
     * Purchase movements
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchaseMovements()
    {
        return $this->hasMany(PurchaseMovement::class);
    }

    /**
     * My purchases
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeMy(Builder $query)
    {
        if (! Auth::user()->isAdmin()) {
            $query->where('buyer_id', Auth::user()->id);
        }

        return $query;
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
