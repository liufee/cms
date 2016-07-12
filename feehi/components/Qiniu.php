<?php

namespace feehi\components;

use yii;
use yii\base\Object;

class Qiniu extends Object
{

    public $enable = false;
    public $accessKey;
    public $secretKey;
    public $bucket;
    public $directory = '';
    private $auth;

    public function init()
    {
        require yii::getAlias("@app")."/../third/qiniu-7.0.7/autoload.php";
        $this->auth = new \Qiniu\Auth($this->accessKey, $this->secretKey);
    }

    public function uploadFile($key, $file, $bucket='')
    {
        if(!$this->enable) return;
        set_time_limit(0);
        if($bucket != '') $this->bucket = $bucket;
        $token = $this->auth->uploadToken($this->bucket);
        $uploadMgr = new \Qiniu\Storage\UploadManager();
        list($ret, $err) = $uploadMgr->putFile($token, $this->directory.$key, $file);
        if ($err !== null) {
            yii::getLogger()->log("qiniu upload file failed,error file name=>{$file}, error info => {$err}", yii\log\Logger::LEVEL_ERROR);
            return false;
        } else {
            return true;
        }



    }

    public function deleteObject($key, $bucket='')
    {
        if($bucket != '') $this->bucket = $bucket;
        $bucketMgr = new \Qiniu\Storage\BucketManager($this->auth);
        $err = $bucketMgr->delete($this->bucket, $this->directory.$key);
        if ($err !== null) {
            $err = $err->getResponse()->error;
            yii::getLogger()->log("qiniu delete file failed,error file name=>{$key}, error info => {$err}", yii\log\Logger::LEVEL_ERROR);
            return false;
        } else {
            return true;
        }




    }



    public function getOssClient()
    {
        return $this->ossClient;
    }
}