<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2018-05-21 23:38
 */

namespace feehi\base;


use yii\base\Event;

class Mode extends \yii\base\Model
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->on(self::EVENT_AFTER_VALIDATE, [$this, 'customAfterValidate']);
        $this->on(self::EVENT_BEFORE_VALIDATE, [$this, 'customBeforeValidate']);
    }

    public function customAfterValidate(Event $event)
    {

    }

    public function customBeforeValidate(Event $event)
    {

    }

}