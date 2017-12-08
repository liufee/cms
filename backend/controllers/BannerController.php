<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-12-03 21:58
 */

namespace backend\controllers;

use yii;
use backend\actions\CreateAction;
use backend\actions\DeleteAction;
use backend\actions\UpdateAction;
use backend\models\form\BannerForm;
use common\models\Options;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\UnprocessableEntityHttpException;

class BannerController extends \yii\web\Controller
{

    public function actions()
    {
        return [
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => BannerForm::className(),
                'scenario' => 'type',
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => BannerForm::className(),
                'scenario' => 'type',
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => BannerForm::className(),
            ]
        ];
    }

    public function actionIndex()
    {
        $model = new BannerForm(['scenario' => 'type']);
        $query = $model->find()->where(['type' => Options::TYPE_BANNER]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionBanners($id)
    {
        $form = new BannerForm();
        $banners = $form->getBannersById($id);
        $dataProvider = new ArrayDataProvider([
            'allModels' => $banners,
        ]);
        return $this->render('banners', [
            'dataProvider' => $dataProvider,
            'banner' => $form->getBannerTypeById($id)
        ]);
    }

    public function actionBannerCreate($id)
    {
        $model = new BannerForm(['scenario' => 'banner']);
        if (yii::$app->getRequest()->getIsPost()) {
            if ($model->load(yii::$app->getRequest()->post()) && $model->saveBanner($id)) {
                yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->redirect(['banners', 'id'=>$id]);
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                Yii::$app->getSession()->setFlash('error', $err);
            }
        }
        $model->loadDefaultValues();
        return $this->render('banner-create', [
            'model' => $model,
            'banner' => $model->getBannerTypeById($id)
        ]);
    }

    public function actionBannerUpdate($id, $sign)
    {
        $model = new BannerForm(['scenario' => 'banner']);
        if (yii::$app->getRequest()->getIsPost()) {
            if ($model->load(yii::$app->getRequest()->post()) && $model->saveBanner($id, $sign)) {
                if( yii::$app->getRequest()->getIsAjax() ) {
                    return [];
                }else {
                    yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                    return $this->redirect(['banners', 'id' => $id]);
                }
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                if( yii::$app->getRequest()->getIsAjax() ){
                    throw new UnprocessableEntityHttpException($err);
                }else {
                    yii::$app->getSession()->setFlash('error', $err);
                }
            }
        }
        $model = $model->getBannerBySign($id, $sign);
        return $this->render('banner-update', [
            'model' => $model,
            'banner' => $model->getBannerTypeById($id)
        ]);
    }

    public function actionBannerDelete($id, $sign=null)
    {
        if (yii::$app->getRequest()->getIsPost()) {
            if (! $id) {
                throw new BadRequestHttpException(yii::t('app', "Id doesn't exit"));
            }
            $param = yii::$app->getRequest()->post('sign', null);
            if( $param !== null ){
                $sign = $param;
            }
            if (! $sign) {
                throw new BadRequestHttpException(yii::t('app', "Sign doesn't exit"));
            }
            $signs = explode(',', $sign);
            $errorIds = [];
            $model = new BannerForm();
            foreach ($signs as $one) {
                if (! $model->deleteBanner($id, $one)) {
                    $errorIds[] = $one;
                }
            }
            if (count($errorIds) == 0) {
                return [];
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0];
                }
                if ($err != '') {
                    $err = '.' . $err;
                }
                throw new UnprocessableEntityHttpException('id ' . implode(',', $errorIds) . $err);
            }
        } else {
            throw new MethodNotAllowedHttpException(yii::t('app', "Delete must be POST http method"));
        }
    }

    public function actionBannerSort($id)
    {
        if (yii::$app->getRequest()->getIsPost()) {
            $post = yii::$app->getRequest()->post();
            if( isset( $post[yii::$app->getRequest()->csrfParam] ) ) {
                unset($post[yii::$app->getRequest()->csrfParam]);
            }
            $model = new BannerForm();
            $banners = $model->getBannersById($id);
            foreach ($post['sort'] as $sign => $sort){
                foreach ($banners as &$banner){
                    if( $banner['sign'] == $sign ){
                        $banner['sort'] = $sort;
                    }
                }
            }
            $model = Options::findOne($id);
            $model->value = json_encode($banners);
            $model->save(false);
        }
        return $this->redirect(['banners', 'id'=>$id]);
    }
}