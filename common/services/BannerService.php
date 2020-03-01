<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-01-30 18:41
 */

namespace common\services;


use backend\models\form\BannerForm;
use backend\models\form\BannerTypeForm;
use backend\models\search\OptionsSearch;
use common\helpers\Util;
use common\libs\Constants;
use common\models\Options;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class BannerService extends Service implements BannerServiceInterface
{

    public function getSearchModel(array $query, array $options = [])
    {
        return new OptionsSearch(['type' => Options::TYPE_BANNER]);
    }

    public function getModel($id, array $options = [])
    {
        return BannerTypeForm::find()->where(['id' => $id, 'type' => Options::TYPE_BANNER])->one();
    }

    public function newModel(array $options = [])
    {
        return new BannerTypeForm();
    }


    public function newBannerModel($id)
    {
        $model = $this->getDetail($id);
        return new BannerForm(['id'=>$model->id, 'tips'=>$model->tips]);
    }

    public function getBannerList($id)
    {
        $banners = $this->getBanners($id);
        return [
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $banners,
            ])
        ];
    }

    public function getBannerDetail($id, $sign)
    {
        $banners = $this->getBanners($id);
        foreach ($banners as $banner){
            if( $banner->sign === $sign ){
                return $banner;
            }
        }
        throw new NotFoundHttpException("Not found banner id " . $id . " sign " . $sign);
    }

    public function updateBanner($id, $sign, array $postData = [])
    {
        /** @var BannerForm $banner */
        $banner = $this->getBannerDetail($id, $sign);
        if( $banner->load($postData) && $banner->validate() ){
            $model = Options::findOne($id);
            $newBanners = [];
            $banners = $this->getBanners($id);
            foreach ($banners as $b){
                /** @var BannerForm $b */
                if( $b->sign === $sign ){
                    Util::handleModelSingleFileUpload($b, 'img', false, '@uploads/setting/banner/');
                    $banner->img = $b->img;
                    $newBanners[] = $banner->getValue();
                }else {
                    $newBanners[] = $b->getValue();
                }
            }
            $model->value = json_encode($newBanners);
            if( $model->save() ){
                return true;
            }else{
                return $model;
            }
        }else{
            return $banner;
        }
    }

    public function createBanner($id, array $postData = [])
    {
        /** @var BannerForm $banner */
        $banner = $this->newBannerModel($id);
        if ($banner->load($postData) && $banner->validate()) {
            $model = Options::findOne($id);
            $banners = $this->getBanners($id);
            Util::handleModelSingleFileUpload($banner, 'img', true, '@uploads/setting/banner/');
            $banners[] = $banner->getValue();
            $model->value = json_encode($banners);
            if ($model->save()) {
                return true;
            } else {
                return $model;
            }
        } else {
            return $banner;
        }
    }

    public function sortBanner($id, $sign, $sort)
    {
        $newBanners = [];
        $banners = $this->getBanners($id);
        foreach ($banners as $key => $banner) {
            /** @var BannerForm $banner */
            if ($banner->sign === $sign) {
                $banner->sort = $sort;
            }
            $newBanners[] = $banner->getValue();
        }

        $model = Options::findOne($id);
        $model->value = json_encode($banners);
        if ($model->save()) {
            return true;
        } else {
            return $model;
        }
    }

    public function deleteBanner($id, $sign)
    {
        $newBanners = [];
        $banners = $this->getBanners($id);
        foreach ($banners as $banner) {
            /** @var BannerForm $banner */
            if ($banner->sign === $sign) {
                if( !empty( $banner->img ) ){
                    Util::deleteThumbnails(Yii::getAlias('@frontend/web') . $banner->img, [], true);
                }
                continue;
            }
            $newBanners[] = $banner->getValue();
        }

        $model = Options::findOne($id);
        $model->value = json_encode($newBanners);
        if ($model->save()) {
            return true;
        } else {
            return $model;
        }
    }

    private function getBanners($id)
    {
        $model = options::findOne(['type' => Options::TYPE_BANNER, 'id' => $id]);
        if( $model === null ){
            throw new NotFoundHttpException("Not exists banner " . $id);
        }
        $items = json_decode($model->value, true);
        $formModel = $this->newBannerModel($id);
        $banners = [];
        ArrayHelper::multisort($items, 'sort');
        foreach ($items as $item){
            $form = clone $formModel;
            $form->setAttributes($item);
            $form->setOldAttributes($item);
            $banners[] = $form;
        }
        return $banners;
    }

    public function getBannersByAdType($type)
    {
        $model = Options::findOne(['type' => Options::TYPE_BANNER, 'name'=>$type]);
        if( $model == null ) throw new NotFoundHttpException("None banner type named " . $type);
        if( $model->value == '' ) $model->value = '[]';
        $banners = json_decode($model->value, true);
        ArrayHelper::multisort($banners, 'sort');
        /** @var $cdn \feehi\cdn\TargetInterface */
        $cdn = Yii::$app->get('cdn');
        foreach ($banners as $k => &$banner){
            if( $banner['status'] == Constants::Status_Disable ) unset($banners[$k]);
            $banner['img'] = $cdn->getCdnUrl($banner['img']);
        }
        return $banners;
    }
}