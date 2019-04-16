<?php

namespace App\Http\Controllers\Api;


use App\Models\Ads;
use Illuminate\Http\Request;

class  AdsController   extends BaseController
{


    //获取广告
    public function getAd(Request $request){

        $response = array('code' => '10000','message'=>'获取成功','data' => []);

        $tag = $request->input('tag','');

        if(empty($tag)){
            $response['code'] = '20001';
            $response['message'] = '缺少广告类型';
        }else{

            $data = Ads::where('tag',$tag)->first();

            if(empty($data)){
                $response['code'] = '20001';
                $response['message'] = '广告不存在';
            }else{

                switch ($data->type){

                    case 1;

                    break;

                    case 2:
                        $data = self::getArticleData($data);
                      break;

                    case 3:

                        break;
                    case 4:
                        break;
                    case 5:
                        $data = self::getArticleData($data);
                        break;
                    case 6:
                        break;


                }
            }
            $response['data'][0] = $data;
        }

        return response()->json($response);
    }

    protected function getHtmlData($data){

    }

    protected function getArticleData($data){

        $content = [];
        if($data->content){
            $images = explode(',',$data->content);
            $urls = explode(',',$data->link);
            foreach ($images as $key =>$image){
                $content[] = [
                    'show_url'=> env('HTTP_ADMIN') . $image,
                    'link_url' => $urls[$key]
                ];
            }
        }

        return [
            'id' => $data->id,
            'title' => $data->title,
            'type' => $data->type,
            'tag' => $data->tag,
            'content' => $content
        ];
    }

    protected function getLinkIdData($data){


    }
}

