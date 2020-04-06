<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-01-30 09:53
 */

namespace common\services;


use backend\models\search\SearchInterface;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

abstract class Service extends \yii\base\BaseObject implements ServiceInterface
{
    /**
     * @param array $options
     * @return mixed
     */
    abstract public function getSearchModel(array $options=[]);

    /**
     * @param $id
     * @param array $options
     *              - scenario string model scenario(you can different with model scenario, every layer has its own definition).
     *
     *
     * @return mixed
     */
    abstract public function getModel($id, array $options=[]);

    /**
     * @param array $options
     *              - scenario string model scenario(you can different with model scenario, every layer has its own definition).
     *              - loadDefaultValues bool fill model with database column default value. default is true.
     * @return mixed
     */
    abstract public function newModel(array $options=[]);

    /**
     * get backend list.
     * first will get list by your provided search model($this->>getSearchModel($options)).
     * if getSearchModel returns null, will fetch all records by page.
     *
     * @param array $query
     * @param array $options
     * @return array
     * @throws Exception
     */
    public function getList(array $query = [], array $options=[])
    {
        $searchModel = $this->getSearchModel($options);
        if( $searchModel === null ){
            /** @var ActiveRecord $model */
            $model = $this->newModel();
            $result = [
                'dataProvider' => new ActiveDataProvider([
                    'query' => $model->find(),
                ]),
            ];
        }else if( $searchModel instanceof SearchInterface ) {
            $dataProvider = $searchModel->search($query, $options);
            $result = [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
            ];
        }else{
            throw new Exception("getSearchModel must return null or backend\models\search\SearchInterface ");
        }
        return $result;
    }

    /**
     * get record detail by primary key(usually column `id`).
     * if record not exists, will throw NotFound exception display a 404 page.
     *
     * @param $id
     * @param array $options
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function getDetail($id, array $options = [])
    {
        $model = $this->getModel($id, $options);
        if( empty($model) ){
            throw new NotFoundHttpException("Record " . $id . " not exists");
        }
        return $model;
    }

    /**
     * do create a record.
     * if data validate error or save data error, will return whole model. otherwise return true.
     *
     * @param array $postData
     * @param array $options
     * @return bool|ActiveRecord
     */
    public function create(array $postData, array $options=[])
    {
        /** @var ActiveRecord $model */
        $model = $this->newModel($options);
        if( $model->load($postData) && $model->save() ){
            return true;
        }
        return $model;
    }

    /**
     * do update a record.
     * if data validate error or update data error, will return whole model. otherwise return true.
     *
     * @param $id
     * @param array $postData
     * @param array $options
     * @return bool|ActiveRecord
     * @throws NotFoundHttpException
     */
    public function update($id, array $postData, array $options=[])
    {
        /** @var ActiveRecord $model */
        $model = $this->getModel($id, $options);
        if( empty($model) ){
            throw new NotFoundHttpException("Record " . $id . " not exists");
        }
        if( $model->load($postData) && $model->save() ){
            return true;
        }
        return $model;
    }

    /**
     * do delete a record.
     * if delete error, will return whole model. otherwise return true.
     *
     * @param $id
     * @param array $options
     * @return bool|ActiveRecord
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function delete($id, array $options=[])
    {
        /** @var ActiveRecord $model */
        $model = $this->getModel($id, $options);
        if( empty($model) ){
            throw new NotFoundHttpException("Record " . $id . " not exists");
        }
        $result = $model->delete();
        if( $result ){
            return true;
        }
        return $model;
    }

    /**
     * do update a record sort.
     * if data validate error or update data error, will return whole model. otherwise return true.
     *
     * @param $id
     * @param $sort
     * @param array $options
     *              - sortField, string, your database table column name. default will be `sort`
     * @return bool|string
     */
    public function sort($id, $sort, array $options=[])
    {
        $sortField = "sort";
        if( isset($options['sortField']) && !empty($options['sortField']) ){
            $sortField = $options['sortField'];
        }
        /** @var ActiveRecord $model */
        $model = $this->getModel($id, $options);
        if( empty($model) ){
            return "Id " . $id . " not exists";
        }
        $model->$sortField = $sort;
        $result = $model->save();
        if ($result){
            return true;
        }
        return $model;
    }

}