<?php

namespace App\Models;

use App\Contract\WeekTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelNotFound extends Model
{
    use HasFactory;
    use WeekTrait;

    protected $table = 'models_not_found';

    protected $fillable = ['product_id'];

    /**
     * Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
