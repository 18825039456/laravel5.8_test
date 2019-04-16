<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class  ProductCategory    extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable;

    const UPDATED_AT = null ;
    const CREATED_AT = null ;

    protected $table = 'product_category';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'icon', 'url', 'parent_id' ,
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    /*protected $hidden = [
        'password_hash',
    ];*/

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


    /**
     * 获取分类属性
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categoryAttr(){
        return $this->hasMany('App\Models\CategoryAttr','category_id');
    }
}