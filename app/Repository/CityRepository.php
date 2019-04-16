<?php

namespace App\Repository;

use App\Models\City;

class CityRepository extends BaseRepository {

    private $_model;
    public function __construct()
    {
        $this->_model = new City();
    }


    /**
     * 获取名称
     *
     * @param $id
     * @return string
     */
    public function getCityNameById($id){

        $city = $this->_model->find($id);

        return empty($city)? '':$city->title;
    }

}