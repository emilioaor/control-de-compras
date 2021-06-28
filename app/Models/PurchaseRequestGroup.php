<?php

namespace App\Models;

use App\Contract\NumberTrait;
use App\Contract\SearchTrait;
use App\Contract\UuidGeneratorTrait;
use App\Contract\WeekTrait;
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
    use NumberTrait;
    use WeekTrait;

    protected $fillable = ['seller_id'];

    protected $search_fields= ['p.model', 'p.description', 'number'];

    protected $number_prefix = 'ORDEN-';

    protected $appends = ['status'];

    /**
     * PurchaseRequest constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        if (! $this->id) {
            if (Auth::check() && Auth::user()->isSeller()) {
                $attributes['seller_id'] = Auth::user()->id;
            }
        }

        parent::__construct($attributes);
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
     * Status
     *
     * @return string
     */
    public function getStatusAttribute()
    {
        return $this->isOpenWeek() ? 'open' : 'closed';
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

    /**
     * By seller
     *
     * @param Builder $query
     * @param int|null $id
     * @return Builder
     */
    public function scopeBySeller(Builder $query, ?int $id)
    {
        if (Auth::check() && Auth::user()->isSeller()) {
            $id = Auth::user()->id;
        }

        return $query->where('seller_id', $id);
    }
}
