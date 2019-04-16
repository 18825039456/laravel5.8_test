<?php

namespace App\Repository;



use App\Models\Store;

class StoreRepository{

    private static $logistics_arr = [1=> '门店自取',2 => '快速配送'];


    private $_model;
    public function __construct(Store $store)
    {
        $this->_model = $store;
    }

    /**
     * 根据主键查找
     *
     * @param $id
     * @param $trashed
     * @return mixed
     */
    public function find($id, $trashed = false)
    {
        if ($trashed) {
            return $this->_model->withTrashed()->findOrFail($id);
        }
        return $this->_model->findOrFail($id);
    }


    /**
     * 获取门店物流类型
     *
     * @param $store_id
     * @return array
     */
    public function getLogistics($store_id){

        $store = $this->find($store_id);
        $losistics = explode(',',$store->logistics);

        $data = [];
        foreach ($losistics as $key){
            $data[] = [
                'type' => intval($key),
                'type_name' => self::$logistics_arr[$key]
            ];
        }
        return $data;
    }



}