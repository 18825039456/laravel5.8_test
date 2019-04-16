<?php

namespace App\Http\Controllers\Api;


use App\Models\CategoryAttr;


use App\Models\ProductCategory;
use Illuminate\Http\Request;

class   CategoryController   extends BaseController
{

    //获取产品分类属性
    public function getCategoryAttr(Request $request){

        $response = array('code' => '10000','message'=>'获取成功','data' => []);

        $categ_id = $request->input('categ_id',0);


        if(empty($categ_id)){
            $response['code'] = '20001';
            $response['message'] = '参数错误';
        }else{

            $list = CategoryAttr::with(['productAttr'=>function($qurey){
                $qurey->select('id','title','icon');
            },'productAttrItem'=>function($qurey) {
                $qurey->with(['productAttrItem'=>function($qurey) {
                    $qurey->select('id','title','parent_id','icon');
                }])->select('id','title','parent_id','icon');
            }])
                ->where('category_id',$categ_id)
                ->get()
                ->all();

            $data = [];
            foreach ($list as $key =>$attr){
                $data[$key] = $attr->productAttr;
                $data[$key]['product_attr_item'] = $attr->productAttrItem;
            }
            $data = self::setIcon($data);

            $response['data'] = $data;
        }
        return response()->json($response);
    }

    protected function setIcon($list){
        foreach ($list as $item){
            $item->icon = env('HTTP_ADMIN') . $item->icon;
            if(!empty($item->productAttrItem)){
               self::setIcon($item->productAttrItem);
            }
        }
        return $list;
    }

}

