<?php

namespace App\Repository;

use App\Exceptions\GeneralException;

/**
 * 抽象的 Repository 类
 *
 * @package App\Repositories
 */
abstract class BaseRepository
{
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
            return $this->query()->withTrashed()->findOrFail($id);
        }
        return $this->query()->findOrFail($id);
    }

    /**
     * 查询资源集合
     *
     * @param bool $query_string
     * @param bool $keys
     * @param int $paginate
     * @param bool $trashed
     * @return mixed
     * @throws GeneralException
     */
    public function getAll($query_string = false, $keys = false, $paginate = 15, $trashed = false)
    {
        try {
            $query = $this->query();
            if ($query_string && is_array($keys)) {
                foreach ($keys as $key => $value) {
                    $query->orWhere($value, 'like', "%$query_string%");
                }
            }

            if ($trashed) {
                $query->withTrashed();
            }

            return $query->paginate($paginate);
        } catch (Exception $exception) {
            throw new GeneralException('未知错误，导致查询失败', 500);
        }
    }

    /**
     * 创建查询构造器
     *
     * @return mixed
     */
    public function query()
    {
        return call_user_func(static::MODEL.'::query');
    }

    /**
     * 序列化模型实例
     *
     * @param array $attributes
     * @return mixed
     */
   // abstract protected function serialization(array $attributes);
}