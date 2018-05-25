<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-13 12:49
 */

namespace backend\controllers;

use Yii;
use yii\helpers\FileHelper;


class ClearController extends \yii\web\Controller
{

    /**
     * 清除后台缓存
     *
     * @return string
     */
    public function actionBackend()
    {
        FileHelper::removeDirectory(Yii::getAlias('@runtime/cache'));
        $paths = [Yii::getAlias('@admin/assets'), Yii::getAlias('@backend/web/assets')];
        foreach ($paths as $path) {
            $fp = opendir($path);
            while (false !== ($file = readdir($fp))) {
                if (! in_array($file, ['.', '..', '.gitignore'])) {
                    FileHelper::removeDirectory($path . DIRECTORY_SEPARATOR . $file);
                }
            }
        }
        Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Success'));
        return $this->render('clear');
    }

    /**
     * 清除后台缓存
     *
     * @return string
     */
    public function actionFrontend()
    {
        FileHelper::removeDirectory(Yii::getAlias('@frontend/runtime/cache'));
        $paths = [Yii::getAlias('@frontend/web/assets')];
        foreach ($paths as $path) {
            $fp = opendir($path);
            while (false !== ($file = readdir($fp))) {
                if (! in_array($file, ['.', '..', '.gitignore'])) {
                    FileHelper::removeDirectory($path . DIRECTORY_SEPARATOR . $file);
                }
            }
        }
        Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Success'));
        return $this->render('clear');
    }


}