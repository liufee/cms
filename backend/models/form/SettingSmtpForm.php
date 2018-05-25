<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-10-21 14:40
 */

namespace backend\models\form;

use Yii;
use common\models\Options;

class SettingSmtpForm extends \common\models\Options
{
    public $smtp_host;

    public $smtp_port = 25;

    public $smtp_username;

    public $smtp_nickname;

    public $smtp_password;

    public $smtp_encryption = 'tls';


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'smtp_host' => Yii::t('app', 'SMTP Host'),
            'smtp_port' => Yii::t('app', 'SMTP Port'),
            'smtp_username' => Yii::t('app', 'SMTP Username'),
            'smtp_nickname' => Yii::t('app', 'Sender'),
            'smtp_password' => Yii::t('app', 'SMTP Password'),
            'smtp_encryption' => Yii::t('app', 'Encryption'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['smtp_host', 'smtp_username', 'smtp_nickname', 'smtp_password', 'smtp_encryption'], 'string'],
            [['smtp_port'], 'integer'],
            [['smtp_host', 'smtp_username'], 'required'],
            [['smtp_port'], 'compare', 'compareValue' => 0, 'operator' => '>='],
            [['smtp_port'], 'compare', 'compareValue' => 65535, 'operator' => '<='],
        ];
    }

    /**
     * 填充smtp配置
     *
     */
    public function getSmtpConfig()
    {
        $names = $this->getNames();
        foreach ($names as $name) {
            $model = self::findOne(['name' => $name]);
            if ($model != null) {
                $this->$name = $model->value;
            } else {
                $this->$name = '';
            }
        }
    }


    /**
     * 写入smtp配置
     *
     * @return bool
     */
    public function setSmtpConfig()
    {
        $names = $this->getNames();
        foreach ($names as $name) {
            $model = self::findOne(['name' => $name]);
            if ($model != null) {
                $model->value = $this->$name;
                $result = $model->save(false);
            } else {
                $model = Yii::createObject( Options::className() );
                $model->name = $name;
                $model->value = $this->$name;
                $result = $model->save(false);
            }
            if ($result == false) {
                return $result;
            }
        }
        return true;
    }

    /**
     * 获取smtp邮箱配置
     *
     * @return array
     */
    public static function getComponentConfig()
    {
        $config = Yii::createObject( self::className() );
        $config->getSmtpConfig();
        return [
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => $config->smtp_host,
                'username' => $config->smtp_username,
                'password' => $config->smtp_password,
                'port' => $config->smtp_port,
                'encryption' => $config->smtp_encryption,

            ],
            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => [$config->smtp_username => $config->smtp_nickname]
            ],
        ];
    }

}