<?php

namespace App\Http\Controllers\Api;


use App\Models\Ads;
use Illuminate\Http\Request;

class  BaseController   extends Controller
{

    public function __construct()
    {
        header("content-Type:application/json;charset=utf-8");
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Credentials:true");
    }

}

