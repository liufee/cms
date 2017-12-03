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
        ]);
    }

    public function actionBannerUpdate($id, $img)
    {
        $model = new BannerForm(['scenario' => 'banner']);
        if (yii::$app->getRequest()->getIsPost()) {
            if ($model->load(yii::$app->getRequest()->post()) && $model->saveBanner($id, $img)) {
                yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->redirect(['banners', 'id' => $id]);
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                Yii::$app->getSession()->setFlash('error', $err);
            }
        }
        $model = $model->getBannerByImg($id, $img);
        return $this->render('banner-update', [
            'model' => $model,
        ]);
    }

    public function actionBannerDelete($id, $img)
    {
        if (yii::$app->getRequest()->getIsAjax()) {//AJAX删除
            if (! $id) {
                throw new BadRequestHttpException(yii::t('app', "Id doesn't exit"));
            }
            if (! $img) {
                throw new BadRequestHttpException(yii::t('app', "Img doesn't exit"));
            }
            $imgs = explode(',', $img);
            $errorIds = [];
            /* @var $model yii\db\ActiveRecord */
            $model = null;
            foreach ($imgs as $one) {

                if ($model) {
                    if (! $result = $model->delete()) {
                        $errorIds[] = $one;
                    }
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
            if (! $id) {
                throw new BadRequestHttpException(yii::t('app', "Id doesn't exit"));
            }
            if (! $img) {
                throw new BadRequestHttpException(yii::t('app', "Img doesn't exit"));
            }
            $model = new BannerForm();
            $model->deleteBanner($id, $img);
            return $this->redirect(yii::$app->request->headers['referer']);
        }
    }
}