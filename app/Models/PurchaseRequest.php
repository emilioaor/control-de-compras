<?php

namespace App\Models;

use App\Contract\SearchTrait;
use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRequest extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UuidGeneratorTrait;
    use SearchTrait;

    /** Status */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSED = 'processed';

    protected $fillable = ['product_id', 'qty', 'approved', 'refused'];

    protected $search_fields= ['status'];

    protected $dates = ['processed_at'];

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
}
