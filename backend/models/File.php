<?php
/**
 * Ahthor: lf
 * Email: job@feehi.com
 * Blog: http://blog.feehi.com
 * Date: 2016/9/1 15:59
 */
namespace backend\models;

use yii;

class File extends \common\models\File
{
    public function beforeDelete()
    {
        if( $this->status != self::STATUS_UNUSED ){
            $this->addError('status', yii::t('app', 'This file is in use,cannot be deleted'));
            return false;
        }
       $uri = str_replace(yii::$app->params['site']['url'], yii::getAlias('@frontend/web'), $this->uri);
        if( file_exists($uri) && !unlink($uri) ) {
            $this->addError("filename", yii::t('app', 'Delete file {name} failed', ['name' => $uri]));
            return false;
        }
        return true;
    }
}