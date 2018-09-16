<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\components;

use Yii;
use backend\models\AdminLog as AdminLogModel;

class AdminLog extends \yii\base\Event
{

    /**
     * 数据库新增保存日志
     *
     * @param $event
     * @throws \Throwable
     */
    public static function create($event)
    {
        if ($event->sender->className() !== AdminLogModel::className()) {
            $desc = '<br>';
            foreach ($event->sender->getAttributes() as $name => $value) {
                !is_string( $value ) && $value = print_r($value, true);
                $desc .= $event->sender->getAttributeLabel($name) . '(' . $name . ') => ' . $value . ',<br>';
            }
            $desc = substr($desc, 0, -5);
            $model = new AdminLogModel();
            $class = $event->sender->className();
            $idDes = '';
            if (isset($event->sender->id)) {
                $idDes = '{{%ID%}} ' . $event->sender->id;
            }
            $model->description = '{{%ADMIN_USER%}} [ ' . Yii::$app->getUser()->getIdentity()->username . ' ] {{%BY%}} ' . $class . ' [ ' . $class::tableName() . ' ] ' . " {{%CREATED%}} {$idDes} {{%RECORD%}}: " . $desc;
            $model->route = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
            $model->user_id = Yii::$app->getUser()->getId();
            $model->save();
        }
    }

    /**
     * 数据库修改保存日志
     *
     * @param $event
     * @throws \Throwable
     */
    public static function update($event)
    {
        if (! empty($event->changedAttributes)) {
            $desc = '<br>';
            $oldAttributes = $event->sender->oldAttributes;
            foreach ($event->changedAttributes as $name => $value) {
                if( $oldAttributes[$name] == $value ) continue;
                !is_string( $value ) && $value = print_r($value, true);
                $desc .= $event->sender->getAttributeLabel($name) . '(' . $name . ') : ' . $value . '=>' . $event->sender->oldAttributes[$name] . ',<br>';
            }
            $desc = substr($desc, 0, -5);
            $model = new AdminLogModel();
            $class = $event->sender->className();
            $idDes = '';
            if (isset($event->sender->id)) {
                $idDes = '{{%ID%}} ' . $event->sender->id;
            }
            $model->description = '{{%ADMIN_USER%}} [ ' . Yii::$app->getUser()->getIdentity()->username . ' ] {{%BY%}} ' . $class . ' [ ' . $class::tableName() . ' ] ' . " {{%UPDATED%}} {$idDes} {{%RECORD%}}: " . $desc;
            $model->route = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
            $model->user_id = Yii::$app->getUser()->id;
            $model->save();
        }
    }

    /**
     * 数据库删除保存日志
     *
     * @param $event
     * @throws \Throwable
     */
    public static function delete($event)
    {
        $desc = '<br>';
        foreach ($event->sender->getAttributes() as $name => $value) {
            !is_string( $value ) && $value = print_r($value, true);
            $desc .= $event->sender->getAttributeLabel($name) . '(' . $name . ') => ' . $value . ',<br>';
        }
        $desc = substr($desc, 0, -5);
        $model = new AdminLogModel();
        $class = $event->sender->className();
        $idDes = '';
        if (isset($event->sender->id)) {
            $idDes = '{{%ID%}} ' . $event->sender->id;
        }
        $model->description = '{{%ADMIN_USER%}} [ ' . Yii::$app->getUser()->getIdentity()->username . ' ] {{%BY%}} ' . $class . ' [ ' . $class::tableName() . ' ] ' . " {{%DELETED%}} {$idDes} {{%RECORD%}}: " . $desc;
        $model->route = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
        $model->user_id = Yii::$app->getUser()->id;
        $model->save();
    }

    public static function custom(CustomLog $event)
    {
        $model = new AdminLogModel();
        $model->description = $event->getDescription();
        $model->route = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
        $model->user_id = Yii::$app->getUser()->getId();
        $model->save();
    }

}