<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/23
 * Time: 12:54
 */
namespace backend\models;

use yii;

class SettingCustomForm extends \common\models\Options
{

    public function __get($name)
    {
        
    }

    public function attributeLabels()
    {
        return [
            'seo_title' => yii::t('app', 'Seo Title'),
            'seo_keywords' => yii::t('app', 'Seo Keywords'),
            'seo_description' => yii::t('app', 'Seo Description')
        ];
    }

    public function rules()
    {
        return [
            [['seo_title', 'seo_keywords', 'seo_description'], 'string'],
        ];
    }

    public function getCustomSetting()
    {
        $names = self::findAll([]);
        foreach($names as $name){
            $model = self::findOne(['name' => $name]);
            if($model != null)
            {
                $this->$name = $model->value;
            }
            else
            {
                if(empty($defaultValue) && $this->$name!==null)
                {
                    $defaultValue = $this->$name;
                }
                $model = new Config();
                $model->name = $name;
                $model->value = $defaultValue;
                $model->save();
                $this->$name = $defaultValue;
            }
        }
    }



    public function setSeoConfig(){
        $names = $this->getNames();
        $data = \Yii::$app->request->post('SettingWebsiteForm');
        foreach($names as $name){
            self::updateAll(['value' => $this->$name], ['name' => $name]);
        }
        return true;
    }
}