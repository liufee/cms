<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-01-29 14:42
 */

namespace common\services;


use backend\models\form\AdForm;
use common\models\Options;
use yii\base\Exception;
use yii\data\ActiveDataProvider;

class AdService extends Service implements AdServiceInterface
{
    public function getSearchModel(array $query, array $options = [])
    {
        throw new Exception("no need search");
    }

    public function getModel($id, array $options = [])
    {
        return AdForm::findOne($id);
    }

    public function getNewModel(array $options = [])
    {
        $model = new AdForm();
        $model->loadDefaultValues();
        return $model;
    }

    public function getList(array $query = [], array $options = [])
    {
        return [
            'dataProvider' =>  new ActiveDataProvider([
                'query' => AdForm::find()->where(['type'=>AdForm::TYPE_AD])->orderBy('sort,id'),
            ])
        ];
    }

}