<?php

namespace App\Http\Controllers\Api;


use App\Repository\CityRepository;
use App\Repository\CustomerAdddressRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerAddressController extends BaseController
{

    protected $_model;

    public function __construct(CustomerAdddressRepository $customerAdddressRepository)
    {
        parent::__construct();
        $this->_model = $customerAdddressRepository;

    }


    /**
     * 显示地址列表.
     *
     * @return Response
     */
    public function index()
    {
        $response = array('code' => '10000', 'message' => '获取成功', 'data' => []);
        try {
            $list = $this->_model->getAllList(Auth::id());

            $data = [];
            foreach ($list as $item) {
                $data[] = [
                    'id' => $item->id,
                    'ship_name' => $item->ship_name,
                    'ship_mobile' => $item->ship_mobile,
                    'province_name' => $item->province->title,
                    'city_name' => $item->city->title,
                    'district_name' => $item->district->title,
                    'address' => $item->address,
                    'is_default' => $item->is_default
                ];
            }
            $response['data'] = $data;
        } catch (\Exception $exception) {
            $response['code'] = '50000';
            $response['message'] = '系统错误';
        }

        return response()->json($response);
    }


    /**
     * 创建地址
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $response = array('code' => '10000', 'message' => '新增成功', 'data' => []);

        $ship_name = $request->input('ship_name', '');
        $ship_mobile = $request->input('ship_mobile', '');
        $province_id = $request->input('province_id', 0);
        $city_id = $request->input('city_id', 0);
        $district_id = $request->input('district_id', 0);
        $address = $request->input('address', '');
        $is_default = $request->input('is_default', 0);

        $data = [
            'ship_name' => $ship_name,
            'ship_mobile' => $ship_mobile,
            'province_id' => $province_id,
            'city_id' => $city_id,
            'district_id' => $district_id,
            'address' => $address,
            'is_default' => $is_default
        ];
        $response = self::formatValidation($data);

        if ($response['code'] == '10000') {

            DB::beginTransaction();
            try {
                $data['customer_id'] = Auth::id();
                $info = $this->_model->create($data);

                if (empty($info)) {
                    $response['code'] = '50000';
                    $response['message'] = '新增失败';
                    DB::rollBack();
                }

                DB::commit();

            } catch (\Exception $exception) {

                $response['code'] = '50000';
                $response['message'] = '系统错误';
                DB::rollBack();
            }

        }
        return response()->json($response);
    }


    /**
     * 编辑
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request)
    {
        $response = array('code' => '10000', 'message' => '获取成功', 'data' => []);

        $id = $request->input('id', 0);

        if (empty($id)) {
            $response['code'] = '20001';
            $response['message'] = '参数错误';
        } else {

            try {
                $info = $this->_model->find($id);
                if (empty($info)) {
                    $response['data'] = [];
                } else {
                    $data = [
                        'id' => $info->id,
                        'ship_name' => $info->ship_name,
                        'ship_mobile' => $info->ship_mobile,
                        'city_id' => $info->city_id,
                        'city_name' => $info->city->title,
                        'province_id' => $info->province_id,
                        'province_name' => $info->province->title,
                        'district_id' => $info->district_id,
                        'district_name' => $info->district->title,
                        'address' => $info->address,
                        'is_default' => $info->is_default
                    ];
                    $response['data'][0] = $data;
                }
            } catch (\Exception $exception) {
                $response['code'] = '50000';
                $response['message'] = '系统错误';
            }
        }
        return response()->json($response);
    }

    /**
     * 更新
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request)
    {
        $response = array('code' => '10000', 'message' => '更新成功', 'data' => []);

        $id = $request->input('id', 0);
        $ship_name = $request->input('ship_name', '');
        $ship_mobile = $request->input('ship_mobile', '');
        $province_id = $request->input('province_id', 0);
        $city_id = $request->input('city_id', 0);
        $district_id = $request->input('district_id', 0);
        $address = $request->input('address', '');

        $data = [
            'id' => $id,
            'ship_name' => $ship_name,
            'ship_mobile' => $ship_mobile,
            'province_id' => $province_id,
            'city_id' => $city_id,
            'district_id' => $district_id,
            'address' => $address
        ];

        if (empty($id)) {
            $response['code'] = '20001';
            $response['message'] = '参数错误';
        } else {

            $response = self::formatValidation($data);

            if ($response['code'] == '10000') {

                DB::beginTransaction();
                try {
                    $info = $this->_model->update($data);

                    $response['data'] = $info;
                    if (empty($info)) {
                        $response['code'] = '50000';
                        $response['message'] = '更新失败';
                    }else{
                        $response['message'] = '更新成功';
                    }
                    DB::commit();

                } catch (\Exception $exception) {

                    $response['code'] = '50000';
                    $response['message'] = '系统错误';

                    DB::rollBack();
                }
            }
        }
        return response()->json($response);
    }

    /**
     * 删除
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $response = array('code' => '10000', 'message' => '删除成功', 'data' => []);

        $id = $request->input('id',0);

        if(empty($id)){
            $response['code'] = '20001';
            $response['message'] = '参数为空';
        }else{


            if(!$this->_model->destroy($id)){
                $response['code'] = '50000';
                $response['message'] = '删除失败';
            }
        }
        return response()->json($response);
    }


    protected function formatValidation($data)
    {

        $response = array('code' => '10000', 'message' => '新增成功', 'data' => []);

        if (empty($data['ship_name'])) {
            $response = [
                'code' => '20001',
                'message' => '联系人不能为空',
                'data' => ['ship_name']
            ];

        } elseif (empty($data['ship_mobile'])) {

            $response = [
                'code' => '20001',
                'message' => '联系电话不能为空',
                'data' => ['ship_mobile']
            ];

        } elseif (empty($data['province_id'])) {

            $response = [
                'code' => '20001',
                'message' => '请选择省份',
                'data' => ['province_id']
            ];
        } elseif (empty($data['city_id'])) {
            $response = [
                'code' => '20001',
                'message' => '请选择城市',
                'data' => ['city_id']
            ];

        } elseif (empty($data['district_id'])) {
            $response = [
                'code' => '20001',
                'message' => '请选择区县',
                'data' => ['district_id']
            ];

        } elseif (empty($data['address'])) {
            $response = [
                'code' => '20001',
                'message' => '详细地址不能为空',
                'data' => ['address']
            ];
        }

        return $response;
    }

}