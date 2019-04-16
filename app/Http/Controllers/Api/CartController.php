<?php

namespace App\Http\Controllers\Api;

use App\Service\CartService;
use Illuminate\Http\Request;


class CartController extends BaseController
{

    public function store(Request $request)
    {
        $response = array('code' => '10000', 'message' => '添加成功', 'data' => []);

        $product_id = $request->input('product_id', 0);
        $logistics = $request->input('logistics', 0);
        $store_id = $request->input('store_id',0);

        if (empty($product_id) || empty($logistics)) {
            $response['code'] = 20001;
            $response['message'] = '参数错误';
        }elseif($logistics == 1 && empty($store_id)){
            $response['code'] = 20001;
            $response['message'] = '请选择门店';
        }else{

            $carService= new CartService();

            $res = $carService->add($request);

        }

        return response()->json($response);

    }
}