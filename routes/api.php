<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->get('/userList',function (Request $request){

    return  '你好！';
});
*/


$api->version('v1',['namespace'=> 'App\Http\Controllers\Api'] ,function($api) {
    $api->get('version', function() {
        return response('this is version v1');
    });

    $api->get('categores-list','CategoriesController@getList');

    /// 注册，登录
    /// 注册，登录
    $api->post('login','AuthController@login');
    $api->post('signup','AuthController@signup');
    $api->post('sms','SmsController@send');
    $api->post('retpwd','AuthController@retpwd');

//  商品
    $api->post('product/product-category','ProductController@productCategory');
    $api->post('product/list','ProductController@getList'); //获取分类商品列表
    $api->post('product/details','ProductController@details');
    $api->post('product/store-list','ProductController@getProductStoreList');


//分类
    $api->post('category/category-attr','CategoryController@getCategoryAttr');

//  首页布局
    $api->post('layout/layout-type','LayoutController@layoutType');
    $api->post('layout/layout','LayoutController@layout');
    $api->post('getad','AdsController@getAd');

//首页推荐
    $api->get('recommended/home','RecommendedController@getHomeRecom');

    $api->group(['prefix'=>'/','middleware'=>'auth:api'],function () use ($api){
        $api->post('logout','AuthController@logout');
        $api->post('refresh','AuthController@refreshToken');
        $api->post('reset-pwd','AuthController@resetPwd');

        //用户收货地址
        $api->get('customer_address','CustomerAddressController@index');
        $api->post('customer_address/store','CustomerAddressController@store');
        $api->post('customer_address/edit','CustomerAddressController@edit');
        $api->post('customer_address/update','CustomerAddressController@update');
        $api->delete('customer_address/delete','CustomerAddressController@destroy');

        //购物车
        $api->post('cart/store','CartController@store');
    });


//显示html
    $api->get('show-html','showHtmlController@showProductContent');
});