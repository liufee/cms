<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-07 11:37
 */

namespace backend\models;

use common\helpers\Util;
use yii\behaviors\TimestampBehavior;

class FriendlyLink extends \common\models\FriendlyLink
{

    public function init()
    {
        parent::init();
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'beforeSaveEvent']);
        $this->on(self::EVENT_BEFORE_UPDATE, [$this, 'beforeSaveEvent']);
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSaveEvent($event)
    {
        Util::handleModelSingleFileUpload($this, 'image', $event->sender->getIsNewRecord(), '@friendlylink/');
    }
}