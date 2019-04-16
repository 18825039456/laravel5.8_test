<?php
/**
 * 获取html内容
 *
 */
namespace App\Http\Controllers\Api;


use App\Models\Ads;
use App\Models\Product;
use Illuminate\Http\Request;

class  showHtmlController   extends BaseController
{

    public function showProductContent(Request $request){

        $type = $request->input('type','');
        switch ($type){
            case 'product_content':
                self::productContent($request);
                break;
        }

    }

    /**
     * 显示商品详情
     *
     * @param Request $request
     */
    protected function productContent(Request $request){

       $product_id = $request->input('product_id',0);

       $product = Product::where('id',$product_id)->first();

       if($product){

           echo  $product->content;

       }

    }

}

