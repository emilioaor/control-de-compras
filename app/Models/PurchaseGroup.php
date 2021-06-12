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

class PurchaseGroup extends Model
{
    use HasFactory;
    use UuidGeneratorTrait;
    use SearchTrait;
    use SoftDeletes;
    use NumberTrait;
    use WeekTrait;

    protected $fillable = ['buyer_id'];

    protected $search_fields= ['p.model', 'p.description', 'number'];

    protected $number_prefix = 'COMPRA-';

    protected $appends = ['status'];

    /**
     * PurchaseGroup constructor.
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
     * Status
     *
     * @return string
     */
    public function getStatusAttribute()
    {
        return $this->isOpenWeek() ? 'open' : 'closed';
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
            $query->select(['purchase_groups.*'])
                ->join('purchases as ps', 'ps.purchase_group_id', '=', 'purchase_groups.id')
                ->join('products as p', 'p.id', '=', 'ps.product_id')
                ->distinct()
            ;
        }

        return $this->_search($query, $search);
    }

    /**
     * By buyer
     *
     * @param Builder $query
     * @param int|null $id
     * @return Builder
     */
    public function scopeByBuyer(Builder $query, ?int $id)
    {
        if (Auth::check() && Auth::user()->isBuyer()) {
            $id = Auth::user()->id;
        }

        return $query->where('buyer_id', $id);
    }
}
