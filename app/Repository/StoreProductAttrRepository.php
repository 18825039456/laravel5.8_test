<?php

namespace App\Repository;

use App\Models\Product;
use App\Models\StoreProductAttr;

class StoreProductAttrRepository extends BaseRepository {

    private $_model;
    public function __construct()
    {
        $this->_model = new StoreProductAttr();
    }


    /**
     * 获取商品规格
     *
     * @param $id
     * @return mixed
     */
    public function getProductAttrList($product_id){

         $product = Product::find($product_id);
         $image = explode(',',$product->image);

         $product_img = empty($image[0])? '':env('HTTP_ADMIN_STORE') .$image[0];
         $list = $this->_model->where('product_id',$product_id)->get();

         $data = [];
         foreach ($list as $item){
           $attr_key_names = explode(',',$item->attr_key_name);

           $data_attr = [];
           foreach ($attr_key_names as $attr_key_name){
               $attr_key_name = explode(':',$attr_key_name);
               $data_attr[] = [
                   'title' => $attr_key_name[0],
                   'value' => $attr_key_name[1],
               ];
           }
           $data_item['attr'] = $data_attr;
           $data_item['id'] =$item->attr_key;
           $data_item['stock'] = $item->stock;
           $data_item['price'] = floatval($item->price);
           $data_item['image'] = $product_img;

           $data[] = $data_item;
         }
       return $data;

    }

}