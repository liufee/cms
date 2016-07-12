<?php
/**
 * Ahthor: lf
 * Email: job@feehi.com
 * Blog: http://blog.feehi.com
 * Date: 2016/4/811:41
 */
namespace feehi\components;

use yii;
use yii\base\Object;

class Alioss extends Object
{

    public $enable = false;
    public $accessKeyId = '';
    public $accessKeySecret = '';
    public $endpoint = '';
    public $bucket = '';
    public $directory = '';//默认上传在bucket的根目录
    private $ossClient;

    public function init()
    {
        require yii::getAlias("@app")."/../third/aliyun-oss/autoload.php";
        try {
            $this->ossClient = new \OSS\OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
        } catch (OssException $e) {
            print $e->getMessage();
        }
    }

    public function uploadFile($key, $file, $bucket='')
    {
        if(!$this->enable) return;
        set_time_limit(0);
        if($bucket != '') $this->bucket = $bucket;
        $ret = $this->ossClient->uploadFile($this->bucket, $this->directory.$key, $file);
        if( $ret !== null ) {
            yii::getLogger()->log("Article thumb upload to ali oss faield,article title=>{$this->title},error_info=>{$ret}", Logger::LEVEL_ERROR);
            return false;
        }else{
            return true;
        }
    }

    public function deleteObject($object, $bucket='')
    {
        if(!$this->enable) return;
        if($bucket != '') $this->bucket = $bucket;
        try{
            $this->ossClient->deleteObject($this->bucket, $this->directory.$object);
            return true;
        } catch(OssException $e) {
            yii::getLogger()->log("ali oss delete object failed,error object name=>{$object}, error info => {$e->getMessage()}", yii\log\Logger::LEVEL_ERROR);
            return false;
        }
    }

    public function getOssClient()
    {
        return $this->ossClient;
    }
}