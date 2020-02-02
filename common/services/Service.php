<?php


namespace common\services;


use backend\models\search\SearchInterface;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

abstract class Service extends \yii\base\BaseObject implements ServiceInterface
{
    abstract public function getSearchModel(array $query, array $options=[]);
    abstract public function getModel($id, array $options=[]);
    abstract public function getNewModel(array $options=[]);

    public function getList(array $query = [], array $options=[])
    {
        $searchModel = $this->getSearchModel($query, $options);
        if( $searchModel === null ){
            /** @var ActiveRecord $model */
            $model = $this->getNewModel();
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

    public function getDetail($id, array $options = [])
    {
        $model = $this->getModel($id, $options);
        if( empty($model) ){
            throw new NotFoundHttpException("Id " . $id . " not exists");
        }
        return $model;
    }

    public function create(array $postData, array $options=[])
    {
        /** @var ActiveRecord $model */
        $model = $this->getNewModel($options);
        if( $model->load($postData) && $model->save() ){
            return true;
        }
        return $model;
    }

    public function update($id, array $postData, array $options=[])
    {
        /** @var ActiveRecord $model */
        $model = $this->getModel($id, $options);
        if( empty($model) ){
            throw new NotFoundHttpException("Id " . $id . " not exists");
        }
        if( $model->load($postData) && $model->save() ){
            return true;
        }
        return $model;
    }

    public function delete($id, array $options=[])
    {
        /** @var ActiveRecord $model */
        $model = $this->getModel($id, $options);
        if( empty($model) ){
            throw new NotFoundHttpException("Id " . $id . " not exists");
        }
        $result = $model->delete();
        if( $result ){
            return true;
        }
        return $model;
    }

    public function sort($id, $sort, array $options=[])
    {
        $model = $this->getModel($id, $options);
        if( empty($model) ){
            return "Id " . $id . " not exists";
        }
        $model->sort = $sort;
        $result = $model->save();
        if ($result){
            return true;
        }
        return $model;
    }

}