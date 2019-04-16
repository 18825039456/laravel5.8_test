<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class  CategoryAttr   extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable;

    const UPDATED_AT = null ;
    const CREATED_AT = null ;

    protected $table = 'category_attr';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
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

    //获取属性
    public function productAttr(){
        return $this->belongsTo('App\Models\ProductAttr','attr_id');
    }

    public function productAttrItem(){
        return $this->hasMany('App\Models\ProductAttr','parent_id','attr_id');
    }

}