<?php

namespace App\Models;

use App\Contract\SearchTrait;
use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseMovement extends Model
{
    use HasFactory;
    use UuidGeneratorTrait;
    use SearchTrait;

    protected $fillable = ['purchase_id', 'purchase_request_id', 'qty'];

    protected $search_fields = ['qty'];
}
