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
use Yii;
use yii\data\ActiveDataProvider;

class AdController extends \yii\web\Controller
{
    /**
     * @auth
     * - item group=运营管理 category=广告 description-get=列表 sort=620 method=get
     * - item group=运营管理 category=广告 description-get=查看 sort=621 method=get  
     * - item group=运营管理 category=广告 description=创建 sort-get=622 sort-post=623 method=get,post  
     * - item group=运营管理 category=广告 description=修改 sort-get=624 sort-post=625 method=get,post  
     * - item group=运营管理 category=广告 description-post=删除 sort=626 method=post  
     * - item group=运营管理 category=广告 description-post=排序 sort=627 method=post  
     *
     * @return array
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    $dataProvider = Yii::createObject([
                        'class' => ActiveDataProvider::className(),
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