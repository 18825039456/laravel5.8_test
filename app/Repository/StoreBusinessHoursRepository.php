<?php

namespace App\Repository;

use App\Models\StoreBusinessHours;

class StoreBusinessHoursRepository extends BaseRepository {

    private static $week_arr = [
        1 => '星期一',
        2 => '星期二',
        3 => '星期三',
        4 => '星期四',
        5 => '星期五',
        6 => '星期六',
        7 => '星期日'
    ];

    private $_model;

    public function __construct()
    {
        $this->_model = new  StoreBusinessHours();
    }

    /**
     * 获取显示营业时间
     *
     * @param $store_id
     * @return array
     */
    public function getBusinessHoursShow($store_id){

        $hours = $this->_model->where('store_id',$store_id)->get();

        if(empty($hours)){
            return [];
        }




      return ['周一至周日 10：00-22：00'];


    }



}