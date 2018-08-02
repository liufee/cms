<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-07 11:37
 */

namespace backend\models;

use common\helpers\Util;
use Yii;
use yii\behaviors\TimestampBehavior;

class FriendlyLink extends \common\models\FriendlyLink
{

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        Util::handleModelSingleFileUpload($this, 'image', $insert, '@friendlylink/');
        return parent::beforeSave($insert);
    }

    public function beforeDelete()
    {
        if( !empty( $this->image ) ){
            Util::deleteThumbnails(Yii::getAlias('@frontend/web/') . str_replace(Yii::$app->params['site']['url'], '', $this->image), [], true);
        }
        return parent::beforeDelete();
    }
}