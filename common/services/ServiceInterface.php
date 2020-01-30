<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-01-23 09:53
 */

namespace common\services;


interface ServiceInterface
{
    public function getList(array $query = [], array $options=[]);
    public function getModel($id, array $options=[]);
    public function getNewModel(array $options=[]);
    public function create(array $postData, array $options=[]);
    public function update($id, array $postData, array $options=[]);
    public function delete($id, array $options=[]);
    public function sort($id, $sort, array $options=[]);
    public function getDetail($id, array $options=[]);
}