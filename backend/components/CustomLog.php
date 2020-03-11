<?php

/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-09-20 23:04
 */
namespace backend\components;

use yii;
use common\models\AdminUser;
use yii\base\ErrorException;

class CustomLog extends \yii\base\Event
{
    const EVENT_AFTER_CREATE = 1;

    const EVENT_AFTER_DELETE = 2;

    const EVENT_CUSTOM = 3;


    public $description = null;

    private $adminUserName = null;

    public function init()
    {
        parent::init();
        /** @var AdminUser $identity */
        $components = Yii::$app->coreComponents();
        if( !isset($components['user']) ){//cli(console)模式
            $this->adminUserName = "command(console)";
        }else {
            $identity = yii::$app->getUser()->getIdentity();
            $this->adminUserName = $identity->username;
        }
    }

    public function getDescription()
    {
        switch ($this->name){
            case self::EVENT_AFTER_CREATE:
                $description = $this->create();
                break;
            case self::EVENT_AFTER_DELETE:
                $description = $this->delete();
                break;
            case self::EVENT_CUSTOM:
                $description = $this->custom();
                break;
            default:
                throw new ErrorException("None exists event");
        }
        $this->setDescription($description);
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    private function create()
    {
        $class = $this->sender->className();
        $template = $description = '{{%ADMIN_USER%}} [ ' .  $this->adminUserName  . ' ] {{%BY%}} ' . $class . " {{%CREATED%}} {{%RECORD%}}: ";
        if( $this->description !== null ){
            return $template . $this->description;
        }
        switch ($this->sender->className()){
            default:
        $str = "<br>";
            foreach ($this->sender->activeAttributes() as $field) {
                $value = $this->sender->$field;
                if( is_array($value) ) $value = implode(',', $value);
                $str .= $this->sender->getAttributeLabel($field) . '(' . $field . ') => ' . $value . ',<br>';
            }
            $str = substr($str, 0, -5);
         }
       return $template . $str;
    }

    private function delete()
    {
        $class = $this->sender->className();
        $template = '{{%ADMIN_USER%}} [ ' .  $this->adminUserName  . ' ] {{%BY%}} ' . $class . " {{%DELETED%}} {{%RECORD%}}: ";
        if( $this->description !== null ){
            return $template . $this->description;
        }
        switch ($this->sender->className()){
            default:
                $str = "<br>";
                foreach ($this->sender->activeAttributes() as $field) {
                    $value = $this->sender->$field;
                if( is_array($value) ) $value = implode(',', $value);
                $str .= $this->sender->getAttributeLabel($field) . '(' . $field . ') => ' . $value . ',<br>';
            }
            $str = substr($str, 0, -5);

            }
        return $template . $str;
    }

    private function custom()
    {
        $class= $this->sender->className();
        $template = '{{%ADMIN_USER%}} [ ' .  $this->adminUserName  . ' ] {{%BY%}} ' . $class;
        if ($this->description !== null){
            return $template . $this->description;
        }
        switch ($this->sender->className()){
            default:
                throw new ErrorException("EVENT_CUSTOM must set description property");
        }
    }

}