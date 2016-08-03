<?php
/**
 * Created by PhpStorm.
 * User: lf
 * Date: 16/4/1
 * Time: 19:50
 */
namespace feehi\components;

use yii;
use yii\helpers\Url;
use backend\models\AdminLog as AdminLogModel;

class AdminLog
{

    public static function create($event)
    {
        if($event->sender->className() !== AdminLogModel::className()) {
            $desc = '';//var_dump($event);die;
            foreach ($event->sender->getAttributes() as $name => $value) {
                $desc .= $name . ':' . $value . "=>" . $event->sender->getAttribute($name) . ',';
            }
            $desc = substr($desc, 0, -1);
            $model = new AdminLogModel();
            $id = '';
            if(isset($event->sender->id)) $id = ' id:' . $event->sender->id . '的';
            $model->description = yii::$app->user->identity->username . '创建了' . $event->sender->className() . $id . $desc;
            $model->route = Url::to();
            $model->user_id = yii::$app->user->id;
            $model->save();
        }
    }

    public static function update($event)
    {
        if(!empty($event->changedAttributes)){
            $desc = '';
            foreach ($event->changedAttributes as $name => $value){
                $desc .= $name . ':'. $value . "=>" . $event->sender->getAttribute($name) . ',';
            }
            $desc = substr($desc, 0, -1);
            $model = new AdminLogModel();
            $id = '';
            if(isset($event->sender->id)) $id = ' id:' . $event->sender->id . '的';
            $model->description = yii::$app->user->identity->username . '修改了' . $event->sender->className() . $id . $desc;
            $model->route = Url::to();
            $model->user_id = yii::$app->user->id;
            $model->save();
        }
    }

    public static function delete($event)
    {
        $desc = '';//var_dump($event);die;
        foreach ($event->sender->getAttributes() as $name => $value) {
            $desc .= $name . ':' . $value . ',';
        }
        $desc = substr($desc, 0, -1);
        $model = new AdminLogModel();
        $id = '';
        if(isset($event->sender->id)) $id = ' id:' . $event->sender->id . '的';
        $model->description = yii::$app->user->identity->username . '删除了' . $event->sender->className() . $id . $desc;
        $model->route = Url::to();
        $model->user_id = yii::$app->user->id;
        $model->save();
    }
}