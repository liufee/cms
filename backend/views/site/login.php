<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use backend\assets\AppAsset;//use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

AppAsset::register($this);
$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <?= $this->render("/widgets/_language-js")?>
</head>
<body class="gray-bg"><style>.m-t-md{margin-top:0px}</style>
<?php $this->beginBody() ?>
<div class="middle-box text-center loginscreen  animated fadeInDown">
    <?=$this->render('/widgets/_flash') ?>
    <div>
        <div>

            <h1 class="logo-name">H+</h1>

        </div>
        <h3><?=yii::t('app', 'Welcome to')?> Feehi CMS</h3>
        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
        <?= $form->field($model, 'username',['template'=>"{input}\n{error}\n{hint}"])->textInput(['autofocus' => true,'placeholder'=>yii::t("app", "Username")]) ?>

        <?= $form->field($model, 'password',['template'=>"{input}\n{error}\n{hint}"])->passwordInput(['placeholder'=>yii::t("app", "Password")]) ?>
        <?= Html::submitButton(yii::t("app", "Login"), ['class' => 'btn btn-primary block full-width m-b', 'name' => 'login-button']) ?>

        <p class="text-muted text-center"> <a href="<?=Url::to(['user/request-password-reset'])?>"><small><?=yii::t('app', 'Forgot password')?></small></a> |
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
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>