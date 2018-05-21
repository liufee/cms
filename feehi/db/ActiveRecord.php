<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2018-05-21 23:16
 */
namespace feehi\db;

use yii\base\Event;

class ActiveRecord extends \yii\db\ActiveRecord
{

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->on(self::EVENT_AFTER_FIND, [$this, 'customAfterFind']);
        $this->on(self::EVENT_BEFORE_UPDATE, [$this, 'customBeforeSave']);
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'customBeforeSave']);
        $this->on(self::EVENT_AFTER_DELETE, [$this, 'customAfterDelete']);
        $this->on(self::EVENT_AFTER_INSERT, [$this, 'customAfterInsert']);
        $this->on(self::EVENT_AFTER_REFRESH, [$this, 'customAfterRefresh']);
        $this->on(self::EVENT_AFTER_UPDATE, [$this, 'customAfterUpdate']);
        $this->on(self::EVENT_BEFORE_DELETE, [$this, 'customBeforeDelete']);
        $this->on(self::EVENT_INIT, [$this, 'customInit']);
        $this->on(self::EVENT_AFTER_VALIDATE, [$this, 'customAfterValidate']);
        $this->on(self::EVENT_BEFORE_VALIDATE, [$this, 'customBeforeValidate']);
    }

    public function customAfterFind($event)
    {

    }

    public function customBeforeSave($event)
    {

    }

    public function customAfterDelete($event)
    {

    }

    public function customAfterInsert($event)
    {

    }

    public function customAfterRefresh($event)
    {

    }

    public function customAfterUpdate($event)
    {

    }

    public function customBeforeDelete($event)
    {

    }

    public function customInit($event)
    {

    }

    public function customAfterValidate($event)
    {

    }

    public function customBeforeValidate($event)
    {

    }

}