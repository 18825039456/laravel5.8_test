<?php
namespace App\Service;




use App\Models\StoreProductAttr;
use App\Repository\ProductRepository;
use Illuminate\Http\Request;

class CartService{


    public function add(Request $request){

        $product_price = '';
        $attr_id = '';
        $attr_name = '';

        //判断商品是否存在
        $product_id =$request->get('product_id');
        $product = (new ProductRepository())->find($product_id);

        //获取商品是否有规格
         $product_attr_count = StoreProductAttr::where('product_id',$product_id)->count();
         if($product_attr_count==0){
             //商品没有规格
             if(intval($product->stock)<=0){
                 return response()->json([
                     'code' => 40001,
                     'message' => '库存不足'
                 ]);
             }
         }else{
            //商品有规格
             $product_attr_key = $request->get('product_attr_key');
             if(empty($product_attr_key)){
                 return response()->json([
                     'code' => 20001,
                     'message' => '请选择规格'
                 ]);
             }

             //查询规格库存
             $product_attr = StoreProductAttr::where('attr_key',$product_attr_key)->where('product_id',$product_id)->first();
             if(empty($product_attr)){
                 return response()->json([
                     'code' => 20001,
                     'message' => '商品没有该规格'
                 ]);
             }

             if(intval($product_attr->stock)<=0){
                 return response()->json([
                     'code' => 40001,
                     'message' => '库存不足'
                 ]);
             }






         }

        //查询商品库存


        //添加购物车

    }



}