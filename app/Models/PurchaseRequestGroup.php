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

    protected $table = 'purchase_request_groups';

    protected $fillable = ['seller_id', 'excel_downloaded', 'customer_name'];

    protected $search_fields= ['p.model', 'p.description', 'number', 'customer_name'];

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
     * Booted
     */
    public static function booted()
    {
        static::registerModelEvent('creating', function ($prg) {
            $prg->number = $prg->_nextNumber();
        });
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

    /**
     * Scope report
     *
     * @param Builder $query
     * @param $filters
     * @return Builder
     */
    public function scopeReport(Builder $query, $filters)
    {
        $query
            ->select([
                'purchase_request_groups.number',
                'purchase_request_groups.created_at',
                'b.name as brand',
                'p.upc',
                'p.model',
                'p.description',
                'u.name as seller',
                'purchase_request_groups.customer_name'
            ])
            ->join('purchase_requests as pr', 'pr.purchase_request_group_id', '=', 'purchase_request_groups.id')
            ->join('products as p', 'p.id', '=', 'pr.product_id')
            ->join('brands as b', 'b.id', '=', 'p.brand_id')
            ->join('users as u', 'u.id', '=', 'purchase_request_groups.seller_id')
            ->whereBetween('purchase_request_groups.created_at', self::weekRange())
        ;

        if (isset($filters['upc'])) {
            $query->where('p.upc', $filters['upc']);
        }

        if (isset($filters['model'])) {
            $query->where('p.model', $filters['model']);
        }

        if (isset($filters['brand'])) {
            $query->where('b.name', $filters['brand']);
        }

        return $query;
    }
}
