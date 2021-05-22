<?php

namespace App\Models;

use App\Contract\SearchTrait;
use App\Contract\UuidGeneratorTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use SoftDeletes;
    use UuidGeneratorTrait;
    use SearchTrait;

    /** Roles */
    const ROLE_ADMIN = 'administrator';
    const ROLE_SUPPLIER_MANAGER = 'supplier_manager';
    const ROLE_RIVAL_MANAGER = 'rival_manager';
    const ROLE_SELLER = 'seller';
    const ROLE_BUYER = 'buyer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public $search_fields = ['name', 'email', 'role'];

    /**
     * Is admin?
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Is supplier manager?
     *
     * @return bool
     */
    public function isSupplierManager()
    {
        return $this->role === self::ROLE_SUPPLIER_MANAGER;
    }

    /**
     * Is rival manager?
     *
     * @return bool
     */
    public function isRivalManager()
    {
        return $this->role === self::ROLE_RIVAL_MANAGER;
    }

    /**
     * Is seller?
     *
     * @return bool
     */
    public function isSeller()
    {
        return $this->role === self::ROLE_SELLER;
    }

    /**
     * Is buyer?
     *
     * @return bool
     */
    public function isBuyer()
    {
        return $this->role === self::ROLE_BUYER;
    }

    /**
     * Exclude me from select
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotMe(Builder $query): Builder
    {
        return $query->where('id', '<>', Auth::user()->id)->where('email', '<>', 'emilioaor@gmail.com');
    }

    /**
     * Status available
     *
     * @return array
     */
    public static function rolesAvailable()
    {
        return [
            self::ROLE_ADMIN => __('role.' . self::ROLE_ADMIN),
            // self::ROLE_SUPPLIER_MANAGER => __('role.' . self::ROLE_SUPPLIER_MANAGER),
            // self::ROLE_RIVAL_MANAGER => __('role.' . self::ROLE_RIVAL_MANAGER),
            self::ROLE_SELLER => __('role.' . self::ROLE_SELLER),
            self::ROLE_BUYER => __('role.' . self::ROLE_BUYER),
        ];
    }
}
