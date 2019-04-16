<?php

namespace App\Http\Controllers\Api;


use App\Models\Sms;
use Flc\Dysms\Client;
use Flc\Dysms\Request\SendSms;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class   SmsController   extends BaseController
{
    public function send(Request $request)
    {
        ///18202033355
        $config = [
            'accessKeyId' => 'LTAIkLfybEAYQP3Y',
            'accessKeySecret' => 'o2zPfxS2crDXmsnsNhQdOulaRDoJNd',
        ];

        $code = [
            '1'=> 'SMS_162195034'
        ];

        $mobile = $request->input('mobile');
        $type = $request->input('type',1);
        $expires_in= time() + 60 * 5;
        $sms_code = rand(1000, 9999) ;
        if ($mobile) {

            try {
                $client = new Client($config);
                $sendSms = new SendSms();
                $sendSms->setPhoneNumbers($mobile);
                $sendSms->setSignName('美博哈特');
                $sendSms->setTemplateCode($code[$type]);
                //$msg = $psy['realname'] . ($status == 2 ? '通过' : '未通过');
                $sendSms->setTemplateParam(['code' =>$sms_code ]);
                $smsRes = $client->execute($sendSms);
                if (strtoupper($smsRes->Code) != 'OK') {
                    $msg['code'] = 501 ;
                    $msg['data'] = [] ;
                    $msg['message'] = $smsRes->Message;
                    ////return response()->json( $msg );
                } else {
                    $msg['code'] = 10000;
                    $msg['data']['expires_in'] =$expires_in ;
                    $msg['message'] = "发送成功!" ;
                    //Sms::create( [ 'code' => $sms_code ,  'expires_in' => $expires_in , 'mobile' => $mobile  ]) ;
                    ////  事务处理
                    DB::beginTransaction();
                    try{
                        Sms::create( [ 'code' => $sms_code ,  'expires_in' => $expires_in , 'mobile' => $mobile  ]) ;
                        DB::commit();
                    } catch (\Exception  $e  ) {
                        DB::rollBack();
                    }
                    ///return response()->json($msg);
                }

            } catch ( \Exception $e  ) {
                $msg['code'] = 20002;
                $msg['data'] = $e ;
                $msg['message'] = "参数错误！" ;
                ///return response()->json($msg);
            }

        } else {
            $msg['code'] = 20002;
            $msg['message'] = "参数错误！" ;

        }
        return response()->json($msg);
    }

}

