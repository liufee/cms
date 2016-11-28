<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%file_usage}}".
 *
 * @property string $id
 * @property string $fid
 * @property integer $type
 * @property string $use_id
 * @property string $count
 * @property string $created_at
 */
class FileUsage extends \yii\db\ActiveRecord
{

    const TYPE_ARTICLE_THUMB = 0;
    const TYPE_ARTICLE_BODY = 1;
    const TYPE_FRINEDLYLINK = 2;
    const TYPE_TEXT = [
        self::TYPE_ARTICLE_THUMB => 'article_thumb',
        self::TYPE_ARTICLE_BODY => 'article_body',
        self::TYPE_FRINEDLYLINK => 'friendly_link',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%file_usage}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fid', 'use_id', 'created_at'], 'required'],
            [['fid', 'type', 'use_id', 'count', 'created_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'fid' => Yii::t('app', 'Fid'),
            'type' => Yii::t('app', 'Type'),
            'use_id' => Yii::t('app', 'Use ID'),
            'count' => Yii::t('app', 'Count'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    public function useFile($uri, $use_id, $type=self::TYPE_ARTICLE_THUMB, $count=1)
    {
        $fileModel = File::findOne(['uri'=>$uri]);
        if($fileModel == NULL){
            yii::warning("Cannot find file at type : ".self::TYPE_TEXT[$type].' use id : '. $use_id);
        }else {
            if (($fileUsageModel = self::findOne(['fid' => $fileModel->id, 'type' => $type, 'use_id' => $use_id])) != NULL) {
                $fileUsageModel->count = $fileUsageModel->count + $count;
                $fileUsageModel->save();
            } else {
                $this->type = $type;
                $this->fid = $fileModel->id;
                $this->use_id = $use_id;
                $this->count = $count;
                $this->created_at = time();
                $this->save();
            }
            if ($fileModel->status != File::STATUS_USED) {
                $fileModel->status = File::STATUS_USED;
                $fileModel->save();
            }
        }
        return true;
    }

    public function cancelUseFile($uri, $use_id, $type=self::TYPE_ARTICLE_THUMB)
    {
        $uri = str_replace(yii::$app->params['site']['url'], yii::$app->params['site']['sign'], $uri);
        $fileModel = File::findOne(['uri'=>$uri]);
        if($fileModel == NULL) return true;
        $fileUsageModel = FileUsage::findOne(['fid'=>$fileModel->id, 'use_id'=>$use_id, 'type'=>$type]);
        if( $fileUsageModel == NULL ){

        }
        if($fileUsageModel->count <= 1) {
            $fileUsageModel->delete();
        }else{
            $fileUsageModel->count = $fileUsageModel->count - 1;
            $fileUsageModel->save();
        }
        if( ($fileUsageModel = FileUsage::findOne(['fid'=>$fileModel->id]) ) == null ){
            unlink(yii::getAlias('@frontend/web') . str_replace(yii::$app->params['site']['url'], '', $fileModel->uri));
            $fileModel->delete();
        }
    }

}
