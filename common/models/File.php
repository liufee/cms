<?php

namespace common\models;

use Yii;
use feehi\libs\File as Upload;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "{{%file}}".
 *
 * @property string $id
 * @property string $uri
 * @property string $filename
 * @property string $mime
 * @property string $filesize
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class File extends \yii\db\ActiveRecord
{
    const STATUS_UNUSED = 0;
    const STATUS_USED = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%file}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['filename', 'created_at'], 'required'],
            [['filesize', 'status', 'created_at', 'updated_at'], 'integer'],
            [['uri', 'filename', 'mime'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'uri' => Yii::t('app', 'Uri'),
            'filename' => Yii::t('app', 'Filename'),
            'mime' => Yii::t('app', 'Mime'),
            'filesize' => Yii::t('app', 'Filesize'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public function saveFile($type=FileUsage::TYPE_ARTICLE_THUMB)
    {
        switch($type){
            case FileUsage::TYPE_ARTICLE_THUMB :
                $path = Yii::getAlias('@thumb');
                break;
            case FileUsage::TYPE_FRINEDLYLINK:
                $path = Yii::getAlias('@friendlylink');
                break;
            default : exit('none type named '.$type);
        }
        $upload = new Upload();
        $result = $upload->upload($path);
        if( $result[0] != false ){
            $this->saveFileDb($result[0]);
            return yii::$app->params['site']['sign'].str_replace(yii::getAlias('@frontend/web'), '', $result[0]);
        }else{
            return [$upload->getErrors()];
        }
    }

    public function saveFileDb($uri)
    {
        $fileModel = File::findOne(['uri'=>$uri]);
        if($fileModel == NULL){
            $this->filename = pathinfo($uri)['basename'];
            $this->mime = FileHelper::getMimeType( $uri );
            $this->filesize = filesize($uri);
            $this->uri = yii::$app->params['site']['sign'].str_replace(yii::getAlias('@frontend/web'), '', $uri);
            $this->beforeSave(true);
            $this->status = self::STATUS_UNUSED;
            $this->save();
        }else{
            return true;
        }
    }

    public function getUseCount()
    {
        //return $this->hasMany(File::className(), ['id' => 'fid']);
    }

    public function beforeSave($insert)
    {
        $this->uri = str_replace(yii::$app->params['site']['url'], yii::$app->params['site']['sign'], $this->uri);
        if($insert){
            $this->created_at = time();
        }else{
            $this->updated_at = time();
        }
        return true;
    }

    public function afterFind()
    {
        parent::afterFind();
        if( $this->uri ) $this->uri = str_replace(yii::$app->params['site']['sign'], yii::$app->params['site']['url'], $this->uri);
    }
}
