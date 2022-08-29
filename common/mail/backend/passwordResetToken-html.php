<?php
use yii\helpers\Html;
use common\helpers\Util;

/* @var $this yii\web\View */
/* @var $user common\models\AdminUser */

$resetLink = Util::assembleAbsoluteURL(['admin-user/reset-password', 'token' => $user->password_reset_token]);
?>
<div class="password-reset">
    <p>Hello <?= Html::encode($user->username) ?>,</p>

    <p><?= yii::t('app', 'Follow the link below to reset your password') ?>:</p>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>
