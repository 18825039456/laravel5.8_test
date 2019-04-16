<?php

namespace App\Repository;


use App\Models\Product;

class ProductRepository extends BaseRepository {

    /**
     * 定义数据模型
     */
    const MODEL = Product::class;


    /**
     * 商品详情
     *
     * @param $id
     * @return mixed
     */
    public function details($id){

          $prodct = $this->query()->findOrFail($id);

           $data_images=[];
           if($prodct->image){
               $images = explode(',',$prodct->image);

               foreach ($images as $image){
                   $data_images[] = env('HTTP_ADMIN') . $image;
               }
           }
           $prodct->image = $data_images;
           return $prodct;
    }

}