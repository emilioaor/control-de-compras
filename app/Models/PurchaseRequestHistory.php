<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestHistory extends Model
{
    use HasFactory;

    protected $fillable = ['purchase_request_id', 'qty'];

    /**
     * Purchase request
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class)->withTrashed();
    }
}
