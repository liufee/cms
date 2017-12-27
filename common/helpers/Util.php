<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-12-27 14:53
 */

namespace common\helpers;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class Util
{
    public static function handleModelSingleFileUpload(ActiveRecord &$model, $field, $insert, $uploadPath)
    {
        $upload = UploadedFile::getInstance($model, $field);
        if ($upload !== null) {
            $uploadPath = yii::getAlias($uploadPath);
            if (! FileHelper::createDirectory($uploadPath)) {
                $model->addError($field, "Create directory failed " . $uploadPath);
                return false;
            }
            $fullName = $uploadPath . date('YmdHis') . '_' . uniqid() . '.' . $upload->getExtension();
            if (! $upload->saveAs($fullName)) {
                $model->addError($field, yii::t('app', 'Upload {attribute} error: ' . $upload->error, ['attribute' => yii::t('app', ucfirst($field))]) . ': ' . $fullName);
                return false;
            }
            $model->$field = str_replace(yii::getAlias('@frontend/web'), '', $fullName);
            if( !$insert ){
                $file = yii::getAlias('@frontend/web') . $model->getOldAttribute($field);
                if( file_exists($file) && is_file($file) ) unlink($file);
            }
        } else {
            if( $model->$field === '0' ){//删除
                $file = yii::getAlias('@frontend/web') . $model->getOldAttribute($field);
                if( file_exists($file) && is_file($file) ) unlink($file);
                $model->$field = '';
            }else {
                $model->$field = $model->getOldAttribute($field);
            }
        }
    }
}