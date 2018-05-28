<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2018-05-21 20:09
 */

namespace console\controllers;


use Yii;
use yii\console\Controller;

class FeehiCmsController extends Controller
{

    public function actionGetUploadsFiles()
    {
        ini_set('memory_limit','1024M');
        $this->stdout("获取uploads.zip" . PHP_EOL);
        $this->stdout("环境准备中..." . PHP_EOL);
        $runtime = Yii::getAlias("@frontend/runtime/");
        $zipphp = file_get_contents("http://resource-1251086492.cossh.myqcloud.com/cms/pclzip.lib.php");
        file_put_contents($runtime . 'zip.php', $zipphp);
        if( md5_file($runtime . 'zip.php') != "2334984a0c7c8bd0a05e3cea80b60aae" ){
            $this->stdout("你的网络环境不安全，请通过手动方式下载uploads.zip" . PHP_EOL);
            @unlink($runtime . 'zip.php');
        }
        require_once $runtime . 'zip.php';
        $this->stdout("正在下载uploads.zip请稍后..." . PHP_EOL);
        $uploadsZip = $runtime . "uploads.zip";
        file_put_contents($uploadsZip, file_get_contents("http://resource-1251086492.cossh.myqcloud.com/cms/uploads.zip"));
        $archive = new \PclZip($uploadsZip);
        if ($archive->extract( PCLZIP_OPT_PATH, Yii::getAlias("@frontend/web/uploads"), PCLZIP_OPT_REMOVE_PATH, 'install/release') == 0 ) {
            $this->stdout("获取配图失败，请手动下载uplaods.zip并解压到frontend/web/uploads目录" . PHP_EOL);
        }else{
            $this->stdout("获取配图成功" . PHP_EOL);
        }
        @unlink($runtime . 'zip.php');
        @unlink($uploadsZip);
    }
}