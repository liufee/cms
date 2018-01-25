<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2018-01-22 17:23
 */
namespace backend\behaviors;

use backend\components\search\SearchEvent;

class TimeSearchBehavior extends \yii\base\Behavior
{
    public $createdAtAttribute = 'created_at';

    public $updatedAtAttribute = 'updated_at';

    public $timeAttributes = [];

    public $delimiter = "~";

    public $format = "int";


    public function init()
    {
        parent::init();
        empty($this->timeAttributes) && $this->timeAttributes = [$this->createdAtAttribute => $this->createdAtAttribute, $this->updatedAtAttribute => $this->updatedAtAttribute] ;
    }

    public function events()
    {
        return [
            SearchEvent::BEFORE_SEARCH => 'beforeSearch'
        ];
    }

    public function beforeSearch($event)
    {
        /** @var $event \backend\components\search\SearchEvent */
        foreach ($this->timeAttributes as $filed => $attribute) {
            if($attribute !== null) $timeAt = $event->sender->{$attribute};
            if( !empty($timeAt) ){
                $time = explode($this->delimiter, $timeAt);
                if( $this->format === 'int' ){
                    $startAt = strtotime($time[0]);
                    $endAt = strtotime($time[1]);
                }else{
                    $startAt = $time[0];
                    $endAt = $time[1];
                }
                $event->query->andFilterWhere([
                    'between',
                    $filed,
                    $startAt,
                    $endAt
                ]);
            }
        }
    }
}