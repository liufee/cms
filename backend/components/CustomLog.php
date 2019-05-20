<?php

/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-09-20 23:04
 */
namespace backend\components;

use backend\models\form\RbacForm;
use yii;
use yii\base\ErrorException;

class CustomLog extends \yii\base\Event
{
    const EVENT_AFTER_CREATE = 1;

    const EVENT_AFTER_DELETE = 2;

    const EVENT_CUSTOM = 3;

    public $old = null;

    private $description = null;

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
        $template = $description = '{{%ADMIN_USER%}} [ ' . yii::$app->getUser()->getIdentity()->username . ' ] {{%BY%}} ' . $class . " {{%CREATED%}} {{%RECORD%}}: ";
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
        $template = '{{%ADMIN_USER%}} [ ' . yii::$app->getUser()->getIdentity()->username . ' ] {{%BY%}} ' . $class . " {{%DELETED%}} {{%RECORD%}}: ";
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
        $template = '{{%ADMIN_USER%}} [ ' . yii::$app->getUser()->getIdentity()->username . ' ] {{%BY%}} ' . $class;
        switch ($this->sender->className()){
            case RbacForm::className():
                $detail = '<br>';
                if( $this->sender->getScenario() == 'permission' ) {
                    $which = "权限 {$this->sender->name} ";
                }else{
                    $which = "角色 {$this->sender->name} ";
                }
                $oldAttributes = $this->old->getAttributes();
                foreach ($this->sender->activeAttributes() as $field) {
                    $value = $this->sender->$field;
                    if (is_array($value)) {
                        $value = implode(',', $value);
                    }
                    $oldValue = $oldAttributes[$field];
                    if (is_array($oldValue)) {
                        $oldValue = implode(',', $oldValue);
                    }
                    if ($oldValue == $value) {
                        continue;
                    }
                    $detail .= $this->sender->getAttributeLabel($field) . '(' . $field . ') : ' . $oldValue . '=>' . $value . ',<br>';
                }
                $detail = substr($detail, 0, -5);
                $str = " {{%UPDATED%}} $which {{%RECORD%}} " . $detail;
                return $template . $str;
        }
        if( $this->description === null ) throw new ErrorException("EVENT_CUSTOM must set description property");
         return $template . $this->description;
    }

}