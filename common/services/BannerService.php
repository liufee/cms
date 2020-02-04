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
use common\models\Options;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\web\NotFoundHttpException;

class BannerService extends Service implements BannerServiceInterface
{

    public function getSearchModel(array $query, array $options = [])
    {
        throw new Exception("Not need");
    }

    public function getModel($id, array $options = [])
    {
        throw new Exception("Not need");
    }

    public function getNewModel(array $options = [])
    {
        throw new Exception("Not need");
    }

    public function getBannerTypeModel($id)
    {
        return BannerTypeForm::find()->where(['id' => $id, 'type' => Options::TYPE_BANNER])->one();
    }

    public function getNewBannerTypeModel()
    {
        return new BannerTypeForm();
    }

    public function getBannerTypeList(array $query = [])
    {
        return [
            'dataProvider' => new ActiveDataProvider([
                'query' => BannerTypeForm::find()->where(['type' => Options::TYPE_BANNER]),
            ])
        ];
    }

    public function createBannerType(array $postData = [])
    {
        $formModel = $this->getNewBannerTypeModel();
        if ($formModel->load($postData) && $formModel->save()) {
            return true;
        }
        return $formModel->getErrors();
    }

    public function getBannerTypeDetail($id)
    {
        $model = $this->getBannerTypeModel($id);
        if ($model === null) {
            throw new NotFoundHttpException("Banner type id " . $id . " not found");
        }
        return $model;
    }

    public function updateBannerType($id, array $postData = [])
    {
        $model = $this->getBannerTypeDetail($id);
        if ($model->load($postData) && $model->save()) {
            return true;
        }
        return $model->getErrors();
    }

    public function deleteBannerType($id)
    {
        $model = $this->getBannerTypeModel($id);
        if( $model->delete() ){
            return true;
        }
        return $model;
    }



    public function getNewBannerModel()
    {
        return new BannerForm();
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
                    $newBanners[] = $banner->getValue();
                }else {
                    $newBanners[] = $b->getValue();
                }
            }
            $model->value = json_encode($newBanners);
            if( $model->save() ){
                return true;
            }else{
                return $model->getErrors();
            }
        }else{
            return $banner->getErrors();
        }
    }

    public function createBanner($id, array $postData = [])
    {
        /** @var BannerForm $banner */
        $banner = $this->getNewBannerModel();
        if ($banner->load($postData) && $banner->validate()) {
            $model = Options::findOne($id);
            $banners = $this->getBanners($id);
            $banners[] = $banner->getValue();
            $model->value = json_encode($banners);
            if ($model->save()) {
                return true;
            } else {
                return $model->getErrors();
            }
        } else {
            return $banner->getErrors();
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
            return $model->getErrors();
        }
    }

    public function deleteBanner($id, $sign)
    {
        $newBanners = [];
        $banners = $this->getBanners($id);
        foreach ($banners as $key => $banner) {
            /** @var BannerForm $banner */
            if ($banner->sign === $sign) {
                continue;
            }
            $newBanners[] = $banner->getValue();
        }

        $model = Options::findOne($id);
        $model->value = json_encode($newBanners);
        if ($model->save()) {
            return true;
        } else {
            return $model->getErrors();
        }
    }

    private function getBanners($id)
    {
        $model = options::findOne(['type' => Options::TYPE_BANNER, 'id' => $id]);
        if( $model === null ){
            throw new NotFoundHttpException("Not exists banner " . $id);
        }
        $items = json_decode($model->value, true);
        $formModel = $this->getNewBannerModel();
        $banners = [];
        foreach ($items as $item){
            $form = clone $formModel;
            $form->setAttributes($item);
            $banners[] = $form;
        }
        return $banners;
    }
}