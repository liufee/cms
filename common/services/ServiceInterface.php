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
    //backend list page
    public function getList(array $query = [], array $options=[]);
    //get model by primary key(usually table `id` column)
    public function getModel($id, array $options=[]);
    //get search model
    public function getSearchModel(array $options=[]);
    //get a new model, for create record
    public function newModel(array $options=[]);

    //backend execute add a new record
    public function create(array $postData, array $options=[]);
    //backend execute update a exists record
    public function update($id, array $postData, array $options=[]);
    //backend execute get record, if not exists will throw 404(NotFound) exception
    public function getDetail($id, array $options=[]);
    //backend execute delete a exists record
    public function delete($id, array $options=[]);

    //backend execute update a exists record sort
    public function sort($id, $sort, array $options=[]);
}