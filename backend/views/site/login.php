<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="middle-box text-center loginscreen  animated fadeInDown">
    <div>
        <div>

            <h1 class="logo-name">H+</h1>

        </div>
        <h3><?=yii::t('app', 'Welcome to')?> Feehi CMS</h3>
        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
            <?= $form->field($model, 'username',['template'=>"{input}\n{error}\n{hint}"])->textInput(['autofocus' => true,'placeholder'=>yii::t("app", "Username")]) ?>

            <?= $form->field($model, 'password',['template'=>"{input}\n{error}\n{hint}"])->passwordInput(['placeholder'=>yii::t("app", "Password")]) ?>
            <?= Html::submitButton(yii::t("app", "Login"), ['class' => 'btn btn-primary block full-width m-b', 'name' => 'login-button']) ?>

            <p class="text-muted text-center"> <a href="#" onclick="forgotPassword()"><small><?=yii::t('app', 'Forgot password')?></small></a> |
                <?php
                if(yii::$app->language == 'en-US') {
                    echo "<a href = ".Url::to(['site/language', 'lang'=>'zh-CN'])." > 简体中文</a >";
                }else{
                    echo "<a href=".Url::to(['site/language', 'lang'=>'en-US']).">English</a>";
                }
                ?>
            </p>

        <?php ActiveForm::end(); ?>
    </div>
</div>
<script>
    function forgotPassword() {
        swal("<?=yii::t('app', 'Please contact admin for reset password')?>", "");
        return false;
    }
</script>