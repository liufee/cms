<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-12-03 21:58
 */

namespace backend\controllers;

use backend\actions\ViewAction;
use common\services\BannerService;
use common\services\BannerServiceInterface;
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
use yii\helpers\Url;

class BannerController extends \yii\web\Controller
{
    /**
     * @auth
     * - item group=运营管理 category=banner类型 description-get=列表 sort=600 method=get
     * - item group=运营管理 category=banner类型 description=创建 sort-get=601 sort-post=602 method=get,post  
     * - item group=运营管理 category=banner类型 description=修改 sort-get=603 sort-post=604 method=get,post  
     * - item group=运营管理 category=banner类型 description-post=删除 sort=605 method=post  
     * - item group=运营管理 category=banner description-get=列表 sort=610 method=get  
     * - item group=运营管理 category=banner description=创建 sort-get=611 sort-post=612 method=get,post  
     * - item group=运营管理 category=banner description-get=查看 sort=613 method=get
     * - item group=运营管理 category=banner description=修改 sort-get=614 sort-post=615 method=get,post  
     * - item group=运营管理 category=banner description-post=排序 sort=616 method=post  
     * - item group=运营管理 category=banner description=删除 sort=617 method=post  
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actions()
    {
        /** @var BannerService $service */
        $service = Yii::$app->get("bannerService");
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function() use($service){
                    $result = $service->getBannerTypeList();
                    return [
                        'dataProvider' => $result['dataProvider'],
                    ];
                }
            ],
            'create' => [
                'class' => CreateAction::className(),
                'create' => function($postData) use($service){
                    return $service->createBannerType($postData);
                },
                'data' => function() use($service){
                    return [
                        'model' => $service->getNewBannerTypeModel(),
                    ];
                }
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'update' => function($id, $postData) use($service){
                    return $service->updateBannerType($id, $postData);
                },
                'data' => function($id) use($service){
                    return [
                        'model' => $service->getBannerTypeDetail($id),
                    ];
                },
                'successRedirect' => ["banner/index"]
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'delete' => function($id) use($service){
                    return $service->deleteBannerType($id);
                },
            ],

            'banners' => [
                'class' => IndexAction::className(),
                'data' => function($id) use($service){
                    $result = $service->getBannerList($id);
                    return [
                        'dataProvider' => $result['dataProvider'],
                        'bannerType' => $service->getBannerTypeDetail($id),
                    ];
                }
            ],
            'banner-create' => [
                'class' => CreateAction::className(),
                'create' => function($postData) use($service){
                    $id = Yii::$app->getRequest()->get("id", null);
                    return $service->createBanner($id, $postData);
                },
                'data' => function() use($service){
                    $id = Yii::$app->getRequest()->get("id", null);
                    return [
                        'model' => $service->getNewBannerModel(),
                        'bannerType' => $service->getBannerTypeDetail($id),
                    ];
                }
            ],
            'banner-view-layer' => [
                'class' => ViewAction::className(),
                'data' => function($id) use($service){
                    $sign = Yii::$app->getRequest()->get("sign", null);
                    return [
                        'model' => $service->getBannerDetail($id, $sign),
                    ];
                },
                'viewFile' => 'view',
            ],
            'banner-update' => [
                'class' => UpdateAction::className(),
                'update' => function($id, $postData) use($service){
                     $sign = Yii::$app->getRequest()->get("sign", null);
                     return $service->updateBanner($id, $sign, $postData);
                },
                'data' => function($id) use($service) {
                    $sign = Yii::$app->getRequest()->get("sign", null);
                    return [
                        'model' => $service->getBannerDetail($id, $sign),
                        'bannerType' => $service->getBannerTypeDetail($id),
                    ];
                },
                'successRedirect' => ['banner/banners', 'id'=>Yii::$app->getRequest()->get('id'), 'sign'=>Yii::$app->getRequest()->get('sign')]
            ],
            'banner-sort' => [
                'class' => SortAction::className(),
                'sort' => function($id, $value) use($service){
                    return $service->sortBanner($id['id'], $id['sign'], $value);
                },
            ],
            'banner-delete' => [
                'class' => DeleteAction::className(),
                'delete' => function($id) use($service){
                    $sign = Yii::$app->getRequest()->get("sign", null);
                    return $service->deleteBanner($id, $sign);
                },
            ],
        ];
    }
}