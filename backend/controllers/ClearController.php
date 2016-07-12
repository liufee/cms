<?php
/**
 * Ahthor: lf
 * Email: job@feehi.com
 * Blog: http://blog.feehi.com
 * Date: 2016/4/1312:49
 */
namespace backend\controllers;

use yii;
use feehi\libs\Help;


class ClearController extends BaseController
{

    public function actionBackend()
    {
        Help::deleteDir(yii::getAlias('@runtime/cache'));
        Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
        return $this->render('backend');
    }

    public function actionFrontend()
    {
        Help::deleteDir(yii::getAlias('@frontend/runtime/cache'));
        Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
        return $this->render('frontend');
    }


}