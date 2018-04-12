<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-12-03 21:58
 */

namespace backend\controllers;

use backend\actions\ViewAction;
use yii;
use backend\actions\IndexAction;
use backend\actions\SortAction;
use backend\models\form\BannerTypeForm;
use backend\actions\CreateAction;
use backend\actions\DeleteAction;
use backend\actions\UpdateAction;
use backend\models\form\BannerForm;
use common\models\Options;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

class BannerController extends \yii\web\Controller
{

    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    $dataProvider = new ActiveDataProvider([
                        'query' => BannerTypeForm::find()->where(['type' => Options::TYPE_BANNER]),
                    ]);
                    return [
                        'dataProvider' => $dataProvider,
                    ];
                }
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => BannerTypeForm::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => BannerTypeForm::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => BannerTypeForm::className(),
            ],

            'banners' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    $id = yii::$app->getRequest()->get('id', null);
                    $form = new BannerForm();
                    $banners = $form->getBanners($id);
                    $dataProvider = new ArrayDataProvider([
                        'allModels' => $banners,
                    ]);
                    return [
                        'dataProvider' => $dataProvider,
                        'bannerType' => BannerTypeForm::findOne($id),
                    ];
                }
            ],
            'banner-create' => [
                'class' => UpdateAction::className(),
                'modelClass' => BannerForm::className(),
            ],
            'banner-view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => BannerForm::className(),
                'viewFile' => 'view',
            ],
            'banner-update' => [
                'class' => UpdateAction::className(),
                'modelClass' => BannerForm::className(),
            ],
            'banner-sort' => [
                'class' => SortAction::className(),
                'modelClass' => BannerForm::className(),
            ],
            'banner-delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => BannerForm::className(),
                'paramSign' => 'sign',
                'scenario' => 'delete',
            ],
        ];
    }
}