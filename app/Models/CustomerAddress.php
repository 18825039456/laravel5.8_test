<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class  CustomerAddress  extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable;

    const UPDATED_AT = null ;
    const CREATED_AT = null ;

    protected $table = 'customer_address';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

    ];

    protected $guarded = ['id'];

    protected $hidden = [
        'sort_order',
    ];

    /**
     * JWT
     *
     * @author AdamTyn
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * JWT
     *
     * @author AdamTyn
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    public function city(){
        return $this->belongsTo(City::class,'city_id');
    }

    public function province(){
        return $this->belongsTo(City::class,'province_id');
    }
    public function district(){
        return $this->belongsTo(City::class,'district_id');
    }



}