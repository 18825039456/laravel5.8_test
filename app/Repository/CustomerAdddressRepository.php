<?php

namespace App\Repository;

use App\Models\CustomerAddress;
use Illuminate\Support\Facades\Auth;

class CustomerAdddressRepository extends BaseRepository
{

    /**
     * 定义数据模型
     */
    const MODEL = CustomerAddress::class;


    /**
     *
     * 获取全部
     *
     * @param $customer_id
     * @return mixed
     *
     */
    public function getAllList($customer_id)
    {
        $list = $this->query()->where('customer_id', $customer_id)->get();
        return $list;
    }


    /**
     * 新增
     *
     * @param $data
     * @return bool
     */
    public function create($data)
    {
        if(isset($data['is_default'])){
            $is_default = $data['is_default'];
        }else{
            $info = $this->query()->where('customer_id', $data['customer_id'])->where('is_default', 1)->first();
            $is_default = empty($info) ? 1 : 0;
        }

        $create_data = [
            'customer_id' => $data['customer_id'],
            'ship_name' => $data['ship_name'],
            'ship_mobile' => $data['ship_mobile'],
            'province_id' => $data['province_id'],
            'city_id' => $data['city_id'],
            'district_id' => $data['district_id'],
            'address' => $data['address'],
            'is_default' => $is_default,
            'created_at' => time(),
            'updated_at' => time()
        ];

        $info  =$this->query()->create($create_data);

        if($info&& $is_default==1){
           $this->query()->where('customer_id', $data['customer_id'])
                ->where('is_default', 1)
                ->whereNotIn('id',[$info->id])
                ->update(['is_default'=>0]);
        }

        return $info;
    }

    /**
     *
     * 更新
     *
     * @param $data
     * @return bool
     */
    public function update($data)
    {
        $info = $this->query()->find($data['id']);

        if ($info&&Auth::id()== $info->customer_id) {
          $info->update($data);
          return true;
        }
        return false;
    }

    /**
     * 删除
     *
     * @param $id
     * @return bool
     */
    public function destroy($id)
    {
        $info = $this->query()->find($id);

        if($info&& Auth::id()== $info->customer_id){
            return  $info->delete();
        }
        return false;
    }


}