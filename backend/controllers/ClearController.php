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
     * @auth - item group=其他 category=缓存 description-get=清除后台缓存 sort=720 method=get
     * @return string
     * @throws \yii\base\ErrorException
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
     * 清除前台缓存
     *
     * @auth - item group=其他 category=缓存 description-get=清除前台缓存 sort=721 method=get
     * @return string
     * @throws \yii\base\ErrorException
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