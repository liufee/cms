<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-12-05 12:47
 */

namespace backend\controllers;

use backend\actions\ViewAction;
use backend\models\form\AdForm;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;
use yii\data\ActiveDataProvider;

class AdController extends \yii\web\Controller
{
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    $dataProvider = new ActiveDataProvider([
                        'query' => AdForm::find()->where(['type'=>AdForm::TYPE_AD])->orderBy('sort,id'),
                    ]);
                    return [
                        'dataProvider' => $dataProvider,
                    ];
                }
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => AdForm::className(),
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => AdForm::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => AdForm::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => AdForm::className(),
            ],
            'sort' => [
                'class' => SortAction::className(),
                'modelClass' => AdForm::className(),
            ],
        ];
    }
}