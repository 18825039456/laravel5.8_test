<?php

namespace App\Repository;

use App\Models\Cart;

class CartRepository extends BaseRepository
{

    /**
     * 定义数据模型
     */
    const MODEL = Cart::class;


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

    }


    /**
     * 新增
     *
     * @param $data
     * @return bool
     */
    public function create($data)
    {


    }




}