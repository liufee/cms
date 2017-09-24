<?php

/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-09-20 23:04
 */
namespace backend\components;

class CustomLog extends \yii\base\Event
{
    const EVENT_AFTER_CREATE = 1;

    const EVENT_AFTER_UPDATE = 2;

    const EVENT_AFTER_DELETE = 3;
}