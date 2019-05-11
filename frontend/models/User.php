<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-02 10:30
 */

namespace frontend\models;

use Exception;
use Yii;
use common\helpers\Util;

/**
 * User model
 *
 * @property string $access_token
 */
class User extends \common\models\User
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'repassword'], 'string'],
            [['avatar'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, webp'],
            [['username', 'email'], 'unique'],
            ['email', 'email'],
            [['repassword'], 'compare', 'compareAttribute' => 'password'],
            [['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            [['username', 'email', 'password', 'repassword'], 'required', 'on' => ['create']],
            [['username', 'email'], 'required', 'on' => ['update']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'Username'),
            'email' => Yii::t('app', 'Email'),
            'old_password' => Yii::t('app', 'Old Password'),
            'password' => Yii::t('app', 'Password'),
            'repassword' => Yii::t('app', 'Repeat Password'),
            'avatar' => Yii::t('app', 'Avatar'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }



    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->generateAuthKey();
            $this->setPassword($this->password);
        }else{
            if( !empty($this->password) && empty($this->repassword) ){
                $this->addError("repassword", Yii::t('yii', '{attribute} must be equal to "{compareValueOrAttribute}".', [
                    'attribute' => yii::t('app', 'Repeat Password'),
                    'compareValueOrAttribute' => yii::t('app', 'Password')
                    ])
                );
                return false;
            }
            $this->setPassword( $this->password );
        }
        Util::handleModelSingleFileUpload($this, 'avatar', $insert, '@frontend/web/uploads/avatar/');
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if( empty($this->avatar) ) return true;
        try {
            Util::deleteThumbnails(Yii::getAlias('@frontend/web') . $this->avatar, [], true);
        }catch (Exception $exception){
            $this->addError("avatar", $exception->getMessage());
            return false;
        }
        return true;
    }

}