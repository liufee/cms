<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/23
 * Time: 12:54
 */
namespace backend\models;

use yii;

class SettingSeoForm extends \common\models\Options
{
    public $seo_title;
    public $seo_keywords;
    public $seo_description;

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

    public function getSeoSetting()
    {
        $names = $this->getNames();
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
        foreach($names as $name){
            $model = self::findOne(['name' => $name]);
            $model->value = $this->$name;
            $model->save();
        }
        return true;
    }
}