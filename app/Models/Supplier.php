<?php

namespace App\Models;

use App\Contract\SearchTrait;
use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UuidGeneratorTrait;
    use SearchTrait;

    protected $fillable = ['name'];

    protected $search_fields = ['name'];

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
     * Purchase groups
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchaseGroups()
    {
        return $this->hasMany(PurchaseGroup::class);
    }
}
