<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-30 19:04
 */

namespace api\models;

use Yii;
use yii\web\IdentityInterface;
use yii\web\UnauthorizedHttpException;

class User extends \frontend\models\User implements IdentityInterface
{
    public function fields()
    {
        $fields = parent::fields();
        unset($fields['auth_key'], $fields['password_hash'], $fields['password_reset_token'], $fields['access_token']);
        return $fields;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        if( !self::accessTokenIsValid($token) ){
            throw new UnauthorizedHttpException("token格式错误或已过期");
        }
        return static::findOne(['access_token' => $token]);
    }

    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString(32) . time();

    }

    public static function accessTokenIsValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $timestamp = (int) substr($token, -10);
        $expire = Yii::$app->params['user.apiTokenExpire'];
        return $timestamp + $expire >= time();
    }

}