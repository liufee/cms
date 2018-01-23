<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2018-01-22 17:17
 */
namespace backend\components\search;

class SearchEvent extends \yii\base\Event
{
    const BEFORE_SEARCH = 1;

    /** @var $query \yii\db\ActiveQuery */
    public $query;
}