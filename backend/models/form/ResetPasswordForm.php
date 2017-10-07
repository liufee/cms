<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */
namespace backend\models\form;

use yii;
use yii\base\Event;
use yii\db\BaseActiveRecord;
use yii\base\InvalidParamException;
use backend\models\User;

/**
 * Password reset form
 */
class ResetPasswordForm extends \yii\base\Model
{
    public $password;

    private $_user;


    public function __construct($token, $config = [])
    {
        if (empty($token) || ! is_string($token)) {
            throw new InvalidParamException('Password reset token cannot be blank.');
        }
        $this->_user = User::findByPasswordResetToken($token);
        if (! $this->_user) {
            throw new InvalidParamException('Wrong password reset token.');
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password' => yii::t('app', 'Password'),
        ];
    }

    /**
     * Resets password.
     *
     * @return boolean if password was reset.
     */
    public function resetPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();
        Event::off(BaseActiveRecord::className(), BaseActiveRecord::EVENT_AFTER_UPDATE);

        return $user->save(false);
    }
}
