<?php

namespace App\Models;

use App\Contract\NumberTrait;
use App\Contract\SearchTrait;
use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UuidGeneratorTrait;
    use SearchTrait;
    use NumberTrait;

    protected $fillable = ['upc', 'model', 'description', 'brand_id'];

    protected $search_fields = ['upc', 'model', 'description', 'brands.name'];

    protected $number_prefix = 'PRO-';

    /**
     * Purchase requests
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::class);
    }

    /**
     * Purchases
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
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
     * Products with same model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sameModel()
    {
        return $this->hasMany(self::class, 'model', 'model');
    }

    /**
     * Product prices
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productPrices()
    {
        return $this->hasMany(ProductPrice::class);
    }

    /**
     * Suppliers
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'product_prices');
    }

    /**
     * Brand
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Booted
     */
    public static function booted()
    {
        static::registerModelEvent('creating', function ($product) {
            $product->upc = $product->_nextNumber();
        });
    }

    /**
     * Next number
     */
    public function nextNumber()
    {

    }

    /**
     * Scope model
     *
     * @param Builder $query
     * @param $model
     * @return Builder
     */
    public function scopeModel(Builder $query, $model)
    {
        if ($model) {
            $query->where('model', 'like', "%{$model}%");
        }

        return $query;
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        $query
            ->select(['products.*'])
            ->join('brands', 'brands.id', '=', 'products.brand_id')
        ;

        return $this->_search($query, $search);
    }

    /**
     * Scope report
     *
     * @param Builder $query
     * @param array $filters
     * @return Builder
     */
    public function scopeReport(Builder $query, array $filters)
    {
        if (isset($filters['upc'])) {
            $query->where('upc', $filters['upc']);
        }

        if (isset($filters['model'])) {
            $query->where('model', $filters['model']);
        }

        return $query->orderBy('model')->orderBy('description');
    }
}
