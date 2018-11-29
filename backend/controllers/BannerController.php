<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-12-03 21:58
 */

namespace backend\controllers;

use backend\actions\ViewAction;
use Yii;
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
    /**
     * @auth
     * - item group=运营管理 category=banner类型 description-get=列表 method=get
     * - item group=运营管理 category=banner类型 description=创建 method=get,post  
     * - item group=运营管理 category=banner类型 description=修改 method=get,post  
     * - item group=运营管理 category=banner类型 description-post=删除 method=post  
     * - item group=运营管理 category=banner description-get=列表 method=get  
     * - item group=运营管理 category=banner description=创建 method=get,post  
     * - item group=运营管理 category=banner description-get=查看 method=get
     * - item group=运营管理 category=banner description=修改 method=get,post  
     * - item group=运营管理 category=banner description=排序 method=post  
     * - item group=运营管理 category=banner description=删除 method=post  
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
                    $id = Yii::$app->getRequest()->get('id', null);
                    /** @var BannerForm $form */
                    $form = yii::createObject( BannerForm::className() );
                    $banners = $form->getBanners($id);
                    $dataProvider = Yii::createObject( [
                        'class' => ArrayDataProvider::className(),
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