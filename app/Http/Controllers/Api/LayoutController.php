<?php

namespace App\Http\Controllers\Api;


use App\Models\Layout;
use App\Models\LayoutType;
use App\Models\Sms;
use Flc\Dysms\Client;
use Flc\Dysms\Request\SendSms;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class   LayoutController extends BaseController
{
    //  布局类型
    public function layoutType()
    {
        $response['code'] = 10000;
        $data = LayoutType::where("type", 'home-layout')->orderby("id", "asc")->get()->all();

        if (!empty($data)) {

            foreach ($data as $key => $val) {
                $data[$key]['image'] = env("HTTP_ADMIN") . $data[$key]['image'];
            }

            $response['data'] = $data;
            $response['message'] = '请求成功!';
        } else {
            $response['code'] = 40001;
            $response['message'] = '数据为空!';
        }

        return response()->json($response);
    }

    public function layout()
    {
        /// 首页布局
        $response['code'] = 10000;
        $data = Layout::where("id", '>', 0)->orderby("sort_order", "asc")->get()->all();

        if (!empty($data)) {

            foreach ($data as $key => $val) {
                $imgs = explode(",", $data[$key]['content']);
                foreach ($imgs as $imgs_key => $imgs_val) {
                    $imgs[$imgs_key] = env("HTTP_ADMIN") . $imgs_val;
                }
                $data[$key]['content'] = $imgs;
            }

            $response['data'] = $data;
            $response['message'] = '请求成功!';
        } else {
            $response['code'] = 40001;
            $response['message'] = '数据为空!';
        }

        return response()->json($response);
    }

}