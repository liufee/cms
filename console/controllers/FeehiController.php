<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2018-05-21 20:09
 */

namespace console\controllers;

use console\helpers\FileHelper;
use Yii;
use backend\models\form\RbacForm;
use console\controllers\helpers\BackendAuth;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

class FeehiController extends Controller
{

    public function actionDownloadUploadFiles()
    {
        ini_set('memory_limit','1024M');
        ini_set('default_socket_timeout', 1);
        $uploadsZipUrl = "http://resource-1251086492.cossh.myqcloud.com/cms/uploads.zip";
        $zipPhpUrl = "http://resource-1251086492.cossh.myqcloud.com/cms/pclzip.lib.php";
        $runtime = Yii::getAlias("@frontend/runtime/");
        $this->stdout("正在下载uploads.zip请稍后..." . PHP_EOL);
        $uploadsZip = $runtime . "uploads.zip";
        file_put_contents($uploadsZip, file_get_contents($uploadsZipUrl));
        $this->stdout("下载uploads.zip完成" . PHP_EOL);
        if( extension_loaded("zip") ){
            FileHelper::unzip($uploadsZip, Yii::getAlias("@frontend/web/uploads/"));
        }else {
            $this->stdout("准备解zip包环境..." . PHP_EOL);
            $zipphp = file_get_contents($zipPhpUrl);
            file_put_contents($runtime . 'zip.php', $zipphp);
            if (md5_file($runtime . 'zip.php') != "2334984a0c7c8bd0a05e3cea80b60aae") {
                $this->stdout("你的网络环境不安全，请通过手动方式下载uploads.zip" . PHP_EOL);
                @unlink($runtime . 'zip.php');
                return 1;
            }
            require_once $runtime . 'zip.php';

            $archive = new \PclZip($uploadsZip);
            if ($archive->extract(PCLZIP_OPT_PATH, Yii::getAlias("@frontend/web/uploads"), PCLZIP_OPT_REMOVE_PATH, 'install/release') == 0) {
                $this->stdout("获取配图失败，请手动下载uplaods.zip并解压到frontend/web/uploads目录" . PHP_EOL);
            }
            @unlink($runtime . 'zip.php');
        }
        @unlink($uploadsZip);
        $this->stdout("获取配图成功" . PHP_EOL);
    }

    public function actionPermission()
    {
        /** @var BackendAuth $obj */
        $obj = Yii::createObject([
            'class' => BackendAuth::className(),
        ]);

        $authItems = $obj->getAuthItems();
        $dbAuthItems = $obj->getDbAuthItems();

        $needModifies = [];
        $needAdds = [];
        foreach ($authItems as $authItem){
            //var_dump($dbAuthItems[$authItem['name']]);exit;
           // var_dump($authItem['name']);exit;
            if( isset( $dbAuthItems[$authItem['name']] ) ){

                $data = json_decode($dbAuthItems[$authItem['name']]->data, true);

                if( !(
                    $authItem['name'] === $dbAuthItems[$authItem['name']]->name
                    && $authItem['description'] === $dbAuthItems[$authItem['name']]->description
                    && $authItem['group'] === $data['group']
                    && $authItem['category'] === $data['category']
                    && $authItem['sort'] === $data['sort']
                ) ){
                    $needModifies[] = $authItem;
                }
            }else{
                $needAdds[] = $authItem;
            }
        }

        $needRemoves = array_diff(array_keys($dbAuthItems) ,ArrayHelper::getColumn($authItems, 'name'));

        if( !empty($needAdds) ) {
            if (
                !$this->confirm("确定要增加下面这些规则吗?" . PHP_EOL . implode(PHP_EOL, ArrayHelper::getColumn($needAdds, 'name')) . PHP_EOL, false)
            ) {
                $this->stdout("已取消增加" . PHP_EOL, Console::FG_GREEN);
            } else {
                foreach ($needAdds as $k => $v) {
                    /** @var RbacForm $model */
                    $model = Yii::createObject(['class' => RbacForm::className(), 'scenario' => 'permission']);
                    $model->route = $v['route'];
                    $model->method = $v['method'];
                    $model->description = $v['description'];
                    $model->group = $v['group'];
                    $model->category = $v['category'];
                    $model->sort = $v['sort'];
                    $exits = Yii::$app->getAuthManager()->getPermission($model->route . ':' . $model->method);
                    if (!$exits) {
                        $model->createPermission();
                    }
                    $model->createPermission();
                }
            }
        }

        if( !empty($needModifies) ) {

            if (
                !$this->confirm("确定要修改下面这些规则吗?" . PHP_EOL . implode(PHP_EOL, ArrayHelper::getColumn($needModifies, 'name')) . PHP_EOL, false)
            ) {
                $this->stdout("已取消修改" . PHP_EOL, Console::FG_GREEN);
            } else {
                foreach ($needModifies as $k => $v) {
                    /** @var RbacForm $model */
                    $model = Yii::createObject(['class' => RbacForm::className(), 'scenario' => 'permission']);
                    $model->fillModel($v['name']);
                    $model->route = $v['route'];
                    $model->method = $v['method'];
                    $model->description = $v['description'];
                    $model->group = $v['group'];
                    $model->category = $v['category'];
                    $model->sort = $v['sort'];
                    $model->updatePermission($v['name']);
                }
            }
        }

        if( !empty($needRemoves) ) {
            if (
                !$this->confirm("确定要删除下面这些规则吗?" . PHP_EOL . implode(PHP_EOL, $needRemoves) . PHP_EOL, false)
            ) {
                $this->stdout("已取消删除" . PHP_EOL, Console::FG_GREEN);
            } else {
                foreach ($needRemoves as $k => $v) {
                    /** @var RbacForm $model */
                    $model = Yii::createObject(['class' => RbacForm::className(), 'scenario' => 'permission']);
                    $model->fillModel($v);
                    $model->deletePermission();
                }
            }
        }

        $this->stdout("不受rbac权限控制的控制器" . PHP_EOL);
        $this->stdout(implode(PHP_EOL, $obj->getNoNeedRbacControllers()) . PHP_EOL . PHP_EOL, Console::FG_BLUE);
        $this->stdout("不受权限控制的方法" . PHP_EOL);
        $this->stdout(implode(PHP_EOL, $obj->getNoNeedRbacRoutes()) . PHP_EOL, Console::FG_BLUE);

    }
}