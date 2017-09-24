<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\components;

use yii;
use backend\models\AdminLog as AdminLogModel;
use yii\base\InvalidParamException;
use yii\base\Model;
use backend\form\Model as BackendFormModel;

class AdminLog extends \yii\base\Event
{

    /**
     * 数据库新增保存日志
     *
     * @param $event
     */
    public static function create($event)
    {
        if ($event->sender->className() !== AdminLogModel::className()) {
            $desc = '<br>';
            foreach ($event->sender->getAttributes() as $name => $value) {
                $desc .= $event->sender->getAttributeLabel($name) . '(' . $name . ') => ' . $value . ',<br>';
            }
            $desc = substr($desc, 0, -5);
            $model = new AdminLogModel();
            $class = $event->sender->className();
            $id_des = '';
            if (isset($event->sender->id)) {
                $id_des = '{{%ID%}} ' . $event->sender->id;
            }
            $model->description = '{{%ADMIN_USER%}} [ ' . yii::$app->getUser()->getIdentity()->username . ' ] {{%BY%}} ' . $class . ' [ ' . $class::tableName() . ' ] ' . " {{%CREATED%}} {$id_des} {{%RECORD%}}: " . $desc;
            $model->route = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
            $model->user_id = yii::$app->getUser()->getId();
            $model->save();
        }
    }

    /**
     * 数据库修改保存日志
     *
     * @param $event
     */
    public static function update($event)
    {
        if (! empty($event->changedAttributes)) {
            $desc = '<br>';
            $oldAttributes = $event->sender->oldAttributes;
            foreach ($event->changedAttributes as $name => $value) {
                if( $oldAttributes[$name] == $value ) continue;
                $desc .= $event->sender->getAttributeLabel($name) . '(' . $name . ') : ' . $value . '=>' . $event->sender->oldAttributes[$name] . ',<br>';
            }
            $desc = substr($desc, 0, -5);
            $model = new AdminLogModel();
            $class = $event->sender->className();
            $id_des = '';
            if (isset($event->sender->id)) {
                $id_des = '{{%ID%}} ' . $event->sender->id;
            }
            $model->description = '{{%ADMIN_USER%}} [ ' . yii::$app->getUser()->getIdentity()->username . ' ] {{%BY%}} ' . $class . ' [ ' . $class::tableName() . ' ] ' . " {{%UPDATED%}} {$id_des} {{%RECORD%}}: " . $desc;
            $model->route = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
            $model->user_id = yii::$app->getUser()->id;
            $model->save();
        }
    }

    /**
     * 数据库删除保存日志
     *
     * @param $event
     */
    public static function delete($event)
    {
        $desc = '<br>';
        foreach ($event->sender->getAttributes() as $name => $value) {
            $desc .= $event->sender->getAttributeLabel($name) . '(' . $name . ') => ' . $value . ',<br>';
        }
        $desc = substr($desc, 0, -5);
        $model = new AdminLogModel();
        $class = $event->sender->className();
        $id_des = '';
        if (isset($event->sender->id)) {
            $id_des = '{{%ID%}} ' . $event->sender->id;
        }
        $model->description = '{{%ADMIN_USER%}} [ ' . yii::$app->getUser()->getIdentity()->username . ' ] {{%BY%}} ' . $class . ' [ ' . $class::tableName() . ' ] ' . " {{%DELETED%}} {$id_des} {{%RECORD%}}: " . $desc;
        $model->route = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
        $model->user_id = yii::$app->getUser()->id;
        $model->save();
    }

    public static function customCreate($event)
    {
        $model = new AdminLogModel();
        $desc = "<br>";
        if( $event->sender instanceof Model){//使用form
            foreach ($event->sender->activeAttributes() as $field) {
                $value = $event->sender->$field;
                if( is_array($value) ) $value = implode(',', $value);
                $desc .= $event->sender->getAttributeLabel($field) . '(' . $field . ') => ' . $value . ',<br>';
            }
            $desc = substr($desc, 0, -5);
            $class = $event->sender->className();
            $model->description = '{{%ADMIN_USER%}} [ ' . yii::$app->getUser()->getIdentity()->username . ' ] {{%BY%}} ' . $class . " {{%CREATED%}} {{%RECORD%}}: " . $desc;
        }else{
            $class = $event->sender->className();
            switch ($class){//不同的类名做不同的处理
                case self::className()://特殊的类产生的日子
                    break;
                default:
                    $desc = $event->data;
            }
            $model->description = '{{%ADMIN_USER%}} [ ' . yii::$app->getUser()->getIdentity()->username . ' ] {{%BY%}} ' . $class . " {{%CREATED%}} {{%RECORD%}}: " . $desc;
        }
        $model->route = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
        $model->user_id = yii::$app->getUser()->getId();
        $model->save();
    }

    /**
     * @param $event
     */
    public static function customUpdate($event)
    {
        $model = new AdminLogModel();
        $desc = "<br>";
        if( $event->sender instanceof BackendFormModel){//使用form
            if( $event->sender->getOldModel() == null ) throw new InvalidParamException("Must set oldModel property");
            $oldAttributes = $event->sender->getOldModel()->getAttributes();
            $unchangedDesc = '';
            foreach ($event->sender->activeAttributes() as $field) {
                $value = $event->sender->$field;
                if( is_array($value) ) $value = implode(',', $value);
                $oldValue = $oldAttributes[$field];
                if( is_array($oldValue) ) $oldValue = implode(',', $oldValue);
                if( $oldValue == $value ){
                    $unchangedDesc .= $event->sender->getAttributeLabel($field) . '(' . $field . ') : ' . $value . ',<br>';
                }else {
                    $desc .= $event->sender->getAttributeLabel($field) . '(' . $field . ') : ' . $oldValue . '=>' . $value . ',<br>';
                }
            }
            $desc .= $unchangedDesc;
            $desc = substr($desc, 0, -5);
            $class = $event->sender->className();
            $model->description = '{{%ADMIN_USER%}} [ ' . yii::$app->getUser()->getIdentity()->username . ' ] {{%BY%}} ' . $class . " {{%UPDATED%}} {{%RECORD%}}: " . $desc;
        }else{
            $class = $event->sender->className();
            switch ($class){//不同的类名做不同的处理
                case self::className()://特殊的类产生的日子
                    break;
                default:
                    $desc = $event->data;
            }
            $model->description = '{{%ADMIN_USER%}} [ ' . yii::$app->getUser()->getIdentity()->username . ' ] {{%BY%}} ' . $class . " {{%DELETED%}} {{%RECORD%}}: " . $desc;
        }
        $model->route = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
        $model->user_id = yii::$app->getUser()->getId();
        $model->save();
    }

    public static function customDelete($event)
    {
        $model = new AdminLogModel();
        $desc = "<br>";
        if( $event->sender instanceof Model){//使用form
            foreach ($event->sender->activeAttributes() as $field) {
                $value = $event->sender->$field;
                if( is_array($value) ) $value = implode(',', $value);
                $desc .= $event->sender->getAttributeLabel($field) . '(' . $field . ') => ' . $value . ',<br>';
            }
            $desc = substr($desc, 0, -5);
            $class = $event->sender->className();
            $model->description = '{{%ADMIN_USER%}} [ ' . yii::$app->getUser()->getIdentity()->username . ' ] {{%BY%}} ' . $class . " {{%DELETED%}} {{%RECORD%}}: " . $desc;
        }else{
            $class = $event->sender->className();
            switch ($class){//不同的类名做不同的处理
                case self::className()://特殊的类产生的日子
                    break;
                default:
                    $desc = $event->data;
            }
            $model->description = '{{%ADMIN_USER%}} [ ' . yii::$app->getUser()->getIdentity()->username . ' ] {{%BY%}} ' . $class . " {{%DELETED%}} {{%RECORD%}}: " . $desc;
        }
        $model->route = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
        $model->user_id = yii::$app->getUser()->getId();
        $model->save();
    }

}