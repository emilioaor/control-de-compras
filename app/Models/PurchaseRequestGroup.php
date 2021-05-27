<?php

namespace App\Models;

use App\Contract\SearchTrait;
use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class PurchaseRequestGroup extends Model
{
    use HasFactory;
    use UuidGeneratorTrait;
    use SearchTrait;
    use SoftDeletes;

    /** Status */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSED = 'processed';

    protected $fillable = ['seller_id'];

    protected $search_fields= ['status', 'p.model', 'p.description'];

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
     * Seller
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id')->withTrashed();
    }

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
     * Purchase movements
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchaseMovements()
    {
        return $this->hasMany(PurchaseMovement::class);
    }

    /**
     * My purchase requests
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeMy(Builder $query)
    {
        if (Auth::user()->isSeller()) {
            $query->where('seller_id', Auth::user()->id);
        }

        return $query;
    }

    /**
     * Scope search
     *
     * @param Builder $query
     * @param string|null $search
     * @return Builder
     * @throws \Exception
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if ($search) {
            $query->select(['purchase_request_groups.*'])
                ->join('purchase_requests as pr', 'pr.purchase_request_group_id', '=', 'purchase_request_groups.id')
                ->join('products as p', 'p.id', '=', 'pr.product_id')
                ->distinct()
            ;
        }

        return $this->_search($query, $search);
    }
}
