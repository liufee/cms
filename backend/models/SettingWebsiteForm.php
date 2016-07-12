<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/23
 * Time: 12:54
 */
namespace backend\models;

use yii;

class SettingWebsiteForm extends \common\models\Options
{
    public $website_title;
    public $website_description;
    public $website_email;
    public $webiste_language;
    public $website_icp;
    public $website_statics_script;
    public $website_status;
    public $website_timezone;

    public function attributeLabels()
    {
        return [
            'website_title' => yii::t('app', 'Website Title'),
            'website_description' => yii::t('app', 'Website Description'),
            'website_email' => yii::t('app', 'Website Email'),
            'webiste_language' => yii::t('app', 'Website Language'),
            'website_icp' => yii::t('app', 'Icp Sn'),
            'website_statics_script' => yii::t('app', 'Statics Script'),
            'website_status' => yii::t('app', 'Website Status'),
            'website_timezone' => yii::t('app', 'Website Timezone'),
        ];
    }

    public function rules()
    {
        return [
            [['website_title', 'website_description', 'website_email','webiste_language','website_icp','website_statics_script', 'website_timezone'], 'string'],
            [['website_status'], 'integer'],
        ];
    }

    public function getWebsiteSetting()
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
            }
        }
    }



    public function setWebsiteConfig(){
        $names = $this->getNames();
        $data = \Yii::$app->request->post('SettingWebsiteForm');
        foreach($names as $name){
            self::updateAll(['value' => $this->$name], ['name' => $name]);
        }
        return true;
    }
}