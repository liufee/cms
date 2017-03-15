<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-13 12:49
 */

namespace backend\controllers;

use yii;
use yii\helpers\FileHelper;


class ClearController extends BaseController
{

    public function actionBackend()
    {
        FileHelper::removeDirectory(yii::getAlias('@runtime/cache'));
        Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
        return $this->render('clear');
    }

    public function actionFrontend()
    {
        FileHelper::removeDirectory(yii::getAlias('@frontend/runtime/cache'));
        Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
        return $this->render('clear');
    }


}