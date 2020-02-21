<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-10-16 17:15
 */

namespace common\helpers;

use Yii;
use yii\base\Exception;
use yii\helpers\FileHelper;

class FileDependencyHelper extends \yii\base\BaseObject
{

    /**
     * @var string cache depend file director
     */
    public $rootDir = '@backend/runtime/cache/file_dependency/';

    /**
     * @var string cache depend file name
     */
    public $fileName;


    /**
     * @return bool|string
     * @throws \yii\base\Exception
     */
    public function createFileIfNotExists()
    {
        $cacheDependencyFileName = $this->getDependencyFileName();
        if ( !file_exists(dirname($cacheDependencyFileName)) ) {
            FileHelper::createDirectory(dirname($cacheDependencyFileName));
        }
        if (!file_exists($cacheDependencyFileName)){
            if (! file_put_contents($cacheDependencyFileName, uniqid()) ){
                throw new Exception("create cache dependency file error: " . $cacheDependencyFileName);
            }
        }
        return $cacheDependencyFileName;
    }

    /**
     * update file that invalidate cache
     */
    public function updateFile()
    {
        $cacheDependencyFileName = $this->getDependencyFileName();
        if (file_exists($cacheDependencyFileName)) {
            if ( !file_put_contents($cacheDependencyFileName, uniqid()) ){
                throw new Exception("update cache dependency file error: " . $cacheDependencyFileName);
            }
        }
    }

    /**
     * get full dependency file path (dir + file name)
     *
     * @return bool|string
     */
    private function getDependencyFileName()
    {
        return Yii::getAlias($this->rootDir . $this->fileName);
    }
}