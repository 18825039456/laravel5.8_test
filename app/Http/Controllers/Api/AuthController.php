<?php

namespace App\Http\Controllers\Api;

use App\Models\Sms;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


/*
 *  http://localhost:8080/refresh
 *  header
 *  key: Authorization
 *  value:
 *  Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODA4MFwvbG9naW4iLCJpYXQiOjE1NTM1ODQ2MDMsImV4cCI6MTU1MzU4ODIwMywibmJmIjoxNTUzNTg0NjAzLCJqdGkiOiJob2Uzd2x3VkgyZlZqMkw3Iiwic3ViIjoyLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.kkpY6DbavV9XNadEUsnLKTjEWuF6Az4d8J7E9yfmoFg

    http://localhost:8080/logout

 */

/*
 * 状态码
 *
 *
 * 10000
操作成功。
10001
交易处理中
10002
账户金额不足无法提现
10003
跳转支付
20002
参数有误
20001
参数空
30000
请求错误
30001
用户余额不足
40000
签名有误，签名（requestBody+商户号+秘钥）
40001
不满足条件，报错
50000
系统错误
50001
商户号问题
50002
token有问题
50003
token过期
50004
signature不合法
50005
交互小S的凭证过期
60001
通知后接受处理失败
70001
数据库处理异常
70002
小S账户已绑定系统会员，操作失败
90001
请求环信api出错
90002
请求环信api错误

 *
 *
 */

class AuthController extends BaseController
{
    /**
     * 登录
     *
     * @author AdamTyn
     *
     * @param \Illuminate\Http\Request;
     * @return \Illuminate\Http\Response;
     */
    public function login(Request $request)
    {
        $response = array('code' => '10000','message'=>'登录成功','data' => []);

        $mobile = $request->input('mobile', '');
        $password = $request->input('password', '');
        $code = $request->input('code', '');
        $expires_in = strval(time() + 86400 * 30);
        $type = $request->input('type', 1);

        if ($type == 1) {

            if (!$mobile) {
                $response['code'] = 20001;
                $response['message'] = '手机号不能为空!';
            } elseif (!$password) {
                $response['code'] = 20001;
                $response['message'] = '密码不能为空!';
            } else if ($mobile && $password) {
                try {
                    $user = \App\Models\Customer::where('mobile', $mobile)
                        ->where('password_hash', $password)
                        //->orwhere("email " ,  [ 'email' =>  $request->input('email') ])
                        ->first();
                    // print_r($user);

                    if (empty($user)) {
                        $response['code'] = 20001;
                        $response['message'] = '帐号、密码错误!';
                    } else {

                        if (!$token = Auth::login($user)) {
                            $response['code'] = '5000';
                            $response['msg'] = '系统错误，无法生成令牌';
                        } else {

                            $response['data']['user_id'] = strval($user->id);
                            $response['data']['access_token'] = $token;
                            $response['data']['expires_in'] = $expires_in;
                            \App\Models\Customer::where('username', $request->input('username'))
                                ->where('password_hash', $request->input('password'))->update(['access_token' => $token, 'expires_in' => $expires_in]);
                        }
                    }

                } catch (QueryException $queryException) {
                    $response['code'] = '5002';
                    $response['msg'] = '无法响应请求，服务端异常';
                }
            }
        } else {
            if (!$mobile) {
                $response['code'] = 20001;
                $response['message'] = '手机号不能为空!';
            } elseif (!$code) {
                $response['code'] = 20001;
                $response['message'] = '验证不能为空!';
            } else if ($mobile && $code) {
                if ($this->checkcode($mobile, $code)) {
                    try {

                        $user = \App\Models\Customer::where('mobile', $mobile)
                            //->orwhere("email " ,  [ 'email' =>  $request->input('email') ])
                            ->first();
                        //print_r($user);

                        if (empty($user)) {
                            $response['code'] = 20001;
                            $response['message'] = '帐号、密码错误!';
                        } else {

                            if (!$token = Auth::login($user)) {
                                $response['code'] = '5000';
                                $response['msg'] = '系统错误，无法生成令牌';
                            } else {

                                $response['data']['user_id'] = strval($user->id);
                                $response['data']['access_token'] = $token;
                                $response['data']['expires_in'] = $expires_in;
                                \App\Models\Customer::where('username', $request->input('username'))
                                    ->where('password_hash', $request->input('password'))->update(['access_token' => $token, 'expires_in' => $expires_in]);
                            }
                        }

                    } catch (QueryException $queryException) {
                        $response['code'] = '5002';
                        $response['msg'] = '无法响应请求，服务端异常';
                    }
                } else {
                    $response['code'] = '20001';
                    $response['message'] = '验证码错误!';
                }
            }
        }
        return response()->json($response);
    }

    /**
     * 用户登出
     *
     * @author AdamTyn
     *
     * @return \Illuminate\Http\Response;
     */
    public function logout()
    {
        $response = array('code' => '10000','message'=>'退出成功','data' => []);

        Auth::invalidate(true);

        return response()->json($response);
    }

    /**
     * 更新用户Token
     *
     * @author AdamTyn
     *
     * @param \Illuminate\Http\Request;
     * @return \Illuminate\Http\Response;
     */
    public function refreshToken()
    {
        $response = array('code' => '10000','message'=>'更新成功','data' => []);

        if (!$token = Auth::refresh(true, true)) {
            $response['code'] = '5000';
            $response['msg'] = '系统错误，无法生成令牌';
        } else {
            $response['data']['access_token'] = $token;
            $response['data']['expires_in'] = strval(time() + 86400);
        }

        return response()->json($response);
    }

    /// 用户注册
    public function signup(Request $request)
    {
        $response = array('code' => '10000','message'=>'注册成功','data' => []);

        $mobile = $request->input('mobile', '');
        $password = $request->input('password', '');
        $code = $request->input('code', '');

        if ($this->checkCode($mobile, $code) === false) {
            $response['code'] = '20001';
            $response['message'] = '验证码错误!';
        } elseif ($this->checkMobile($mobile)) {

            ///die( hash("sha256", time() ) ) ;
            $hash = hash("sha256", time());
            try {

                if ($mobile && $password) {

                    $user = Customer::create([
                        'username' => $mobile,
                        'mobile' => $mobile,
                        'password_hash' => md5($password),
                        ///'email' => $email,
                        'auth_key' => $hash,
                        ///'password_hash' => $hash,
                        'updated_at' => time(),
                        'created_at' => time()
                    ]);
                    $response['code'] = 10000;
                    @$response['data']['mobile'] = $mobile;
                    @$response['data']['id'] = $user->id;

                    //print_r($user ->id   ) ;
                    //die() ;
                } else {
                    $response['code'] = '20001';
                    $response['message'] = '参数有误!';
                }

            } catch (\Exception  $e) {
                $response['code'] = '5002';
                $response['message'] = '无法响应请求，服务端异常';
                $response['data']['message'] = $e;
            }
        } else {
            $response['code'] = '20001';
            $response['message'] = '手机号已存在!';
        }
        return response()->json($response);
    }


    //找回密码
    public function retpwd(Request $request)
    {
        $response = array('code' => '10000','message'=>'','data' => []);

        $mobile = $request->input('mobile', '');
        $code = $request->input('code', '');
        $expires_in = strval(time() + 86400 * 30);

        if (empty($mobile)) {
            $response['code'] = 20001;
            $response['message'] = '手机号不能为空!';

        } elseif (!$code) {
            $response['code'] = 20001;
            $response['message'] = '验证吗不能为空!';

        } else if ($this->checkcode($mobile, $code)) {
            try {

                $user = Customer::where('mobile', $mobile)->first();

                if (empty($user)) {
                    $response['code'] = 20001;
                    $response['message'] = '手机号码账号不存在';
                } else {

                    if (!$token = Auth::login($user)) {
                        $response['code'] = '5000';
                        $response['msg'] = '系统错误，无法生成令牌';
                    } else {

                        $data = [
                            'usre_id' => strval($user->id),
                            'access_token' => $token,
                            'expires_in' => $expires_in
                        ];
                        $response['data'][0] = $data;
                        Customer::where('mobile', $mobile)->update(['access_token' => $token, 'expires_in' => $expires_in]);
                    }
                }

            } catch (QueryException $queryException) {
                $response['code'] = '5002';
                $response['msg'] = '无法响应请求，服务端异常';
            }
        } else {
            $response['code'] = '20001';
            $response['message'] = '验证码错误!';
        }
        return response()->json($response);
    }


    //重置密码
    public function resetPwd(Request $request){

        $response = array('code' => '10000','message'=>'重置密码成功','data' => []);

        $password = $request->input('password','');
        $confirmPwd = $request->input('confirm_password','');
        $expires_in = strval(time() + 86400 * 30);

        if(empty($password)){
            $response['code'] = 20001;
            $response['message'] = '密码不能为空!';

        }else if (empty($confirmPwd)){
            $response['code'] = 20001;
            $response['message'] = '确认不能为空!';

        }elseif($password!==$confirmPwd){
            $response['code'] = 20001;
            $response['message'] = '密码不一致!';

        }else{

            // 事务处理
            DB::beginTransaction();
            try{
                Customer::where('id',Auth::user()->id)->update([
                    'password_hash' => $password,
                    'expires_in'=> $expires_in
                ]);
                DB::commit();
            } catch (\Exception  $e  ) {
                $response['code'] = 70001;
                $response['message'] = '修改密码失败!';
                DB::rollBack();
            }
        }

        return response()->json($response);
    }


    public function checkMobile($mobile)
    {
        ///  判断手机号存在

        $one = Customer::where("mobile", $mobile)->first();
        if (empty($one)) {
            return true;
        } else {
            return false;
        }
    }


    public function checkCode($mobile, $code)
    {
        $one = Sms::where('mobile', $mobile)->where('code', $code)->where('expires_in', '>', time())->first();
        if (empty($one)) {
            return false;
        } else {
            return true;
        }
    }
}

