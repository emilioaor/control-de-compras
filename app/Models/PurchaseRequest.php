<?php

namespace App\Models;

use App\Contract\SearchTrait;
use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class PurchaseRequest extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UuidGeneratorTrait;
    use SearchTrait;

    /** Status */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSED = 'processed';

    protected $fillable = ['product_id', 'qty', 'approved', 'refused', 'seller_id'];

    protected $search_fields= ['status', 'products.model', 'products.description'];

    protected $dates = ['processed_at'];

    /**
     * PurchaseRequest constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        if (! $this->id) {
            $this->status = self::STATUS_PENDING;

            if (Auth::check() && Auth::user()->isSeller()) {
               $attributes['seller_id'] = Auth::user()->id;
            }
        }

        parent::__construct($attributes);
    }

    /**
     * Is pending?
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Is processed?
     *
     * @return bool
     */
    public function isProcessed()
    {
        return $this->status === self::STATUS_PROCESSED;
    }

    /**
     * Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Seller
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * My purchase requests
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeMy(Builder $query)
    {
        if (! Auth::user()->isAdmin()) {
            $query->where('seller_id', Auth::user()->id);
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
            ->select(['purchase_requests.*'])
            ->join('products', 'products.id', '=', 'purchase_requests.product_id')
        ;

        return $this->_search($query, $search);
    }
}
