<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-09-04 22:59
 */
namespace frontend\models\form;

use yii;

class ArticlePasswordForm extends \yii\base\Model
{
    public $password;

    public function rules()
    {
        return [
            ['password', 'string', 'max'=>20]
        ];
    }

    public function attributeLabels()
    {
        return [
            "password" => yii::t('app', 'Password'),
        ];
    }

    public function checkPassword($id)
    {
        if( $this->password == Article::findOne($id)['password'] ){
            $session = yii::$app->getSession();
            $session->set("article_password_" . $id, true);
            return true;
        }
        $this->addError('password', yii::t('frontend', 'Password error'));
        return false;
    }
}