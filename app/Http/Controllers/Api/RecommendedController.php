<?php

/**
 * 推荐控制器
 */
namespace App\Http\Controllers\Api;


use App\Models\Ads;
use App\Models\CategoryAttr;


use App\Models\Product;
use App\Models\RecommendedInterest;
use App\Models\RecommendedHome;
use App\Models\RecommendedProduct;
use App\Models\RecommendedStore;
use Illuminate\Http\Request;

class   RecommendedController   extends BaseController
{

    //获取首页推荐

    public function getHomeRecom(){

            $response = array('code' => '10000','message'=>'获取成功','data' => []);

            try {
                $data = [];

                //获取推荐商品
                $goods = Product::where('is_home', 1)->select('id', 'title', 'image', 'price')->get()->all();

                $product_data = [];
                foreach ($goods as $item) {
                    $images = explode(',', $item->image);
                    $image = $images[0] ? env('HTTP_ADMIN') . $images[0] : '';
                    $item->image = $image;
                    $product_data[] = $item;
                }


                //获取推荐广告
                $ads = Ads::where('tag', env('HOME_RECOM_TAG'))->get()->all();


                //获取广告的数量
                $ad_num = intval(count($product_data) / env('HOME_RECOM_AD_INTEREST'));


                //最终推荐的广告
                if ($ad_num >= 1) {
                    if (count($ads) > $ad_num) {
                        $random_keys = array_rand($ads, $ad_num);
                        if (is_array($random_keys)) {
                            foreach ($random_keys as $key) {
                                $recom_ad[] = $ads[$key];
                            }
                        } else {
                            $recom_ad[] = $ads[$random_keys];
                        }
                    } else {
                        $recom_ad = $ads;
                    }
                } else {
                    $recom_ad = [];
                }

                //处理广告
                $data_ad = [];
                foreach ($recom_ad as $key => $ad) {

                    if ($ad['type'] == 3 || $ad['type'] == 4) {
                        //商品，或者店铺
                        $images = explode(',', $ad->content);
                        $image = $images[0] ? env('HTTP_ADMIN') . $images[0] : '';
                        $data_ad[] = [
                            'id' => $ad->link ? intval($ad->link) : 0,
                            'image' => $image,
                            'type' => $ad->type
                        ];

                    } elseif ($ad['type'] == 6) {
                        //搜索
                        $content = explode("\r\n", $ad->content);
                        $data_ad[] = [
                            'type' => $ad->type,
                            'content' => $content
                        ];
                    }
                }
                //插入广告
                $list = [];
                $i = 1;
                foreach ($product_data as $item) {
                    $item->type = 1;
                    $list[] = $item;
                    if (fmod($i, env('HOME_RECOM_AD_INTEREST')) == 0) {
                        $ad = array_pop($data_ad);
                        $list[] = $ad;
                    }
                    $i++;
                }
                $list[] =  [
                    'id' => 1,
                    'type' => 3,
                    'image'=> 'http://192.168.50.110:7001/upload/image/190413/190413035956_30.png',
                ];
                $list[] =  [
                    'id' => 1,
                    'type' => 4,
                    'image'=> 'http://192.168.50.110:7001/upload/image/190413/190413035956_30.png',
                ];
                $list[] =  [
                    'type' => 6,
                    'content' => [
                        "服饰",
                        "女装",
                        "火锅",
                        "电器"]
                ];


                $response['data'] = $list;
            }catch (\Exception $exception){
                $response['code'] = '50000';
                $response['message'] = $exception->getMessage();
            }

        return response()->json($response);
    }



}

