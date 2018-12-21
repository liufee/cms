<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

/**
 * @var $this yii\web\View
 * @var $model \common\models\LoginForm
 */

use backend\assets\AppAsset;
use yii\helpers\Html;
use backend\widgets\ActiveForm;
use yii\helpers\Url;
use yii\captcha\Captcha;

AppAsset::register($this);
$this->title = Yii::t('app', 'Login');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="renderer" content="webkit">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <?= $this->render("/widgets/_language-js") ?>
        <style>
            @media (min-width: 768px){
                div.form-group div.help-block {
                    position: absolute;
                    left: 305px;
                    width: 170px;
                    top: 4px;
                    text-align: left;
                }
            }

            .form-horizontal .form-group {
                width: 300px;
                margin-left: 0px;
            }

            img#loginform-captcha-image{
                position: absolute;
                top: 2px;
                right: 1px;
            }
        </style>
    </head>
    <body class="gray-bg">
    <?php $this->beginBody() ?>
    <div class="middle-box text-center loginscreen  animated fadeInDown">
        <?= $this->render('/widgets/_flash') ?>
        <div>
            <div>
                <h1 class="logo-name">H+</h1>
            </div>
            <h3><?= Yii::t('app', 'Welcome to') ?> Feehi CMS</h3>
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
            <?= $form->field($model, 'username', ['template' => "<div style='position:relative'>{input}\n{error}\n{hint}</div>"])
                ->textInput(['autofocus' => true, 'placeholder' => Yii::t("app", "Username")]) ?>

            <?= $form->field($model, 'password', ['template' => "<div style='position:relative'>{input}\n{error}\n{hint}</div>"])
                ->passwordInput(['placeholder' => Yii::t("app", "Password")]) ?>

            <?= $form->field($model, 'captcha', ['template' => '<div style="position:relative">{input}{error}{hint}</div>'])->widget(Captcha::classname(), [
                'template' => '{input}{image}',
                'options' => [
                    "class"=>"form-control",
                    'style' => "width:300px;height:34px;position:relative;top:2px",
                    'placeholder' => Yii::t("app", "Verification Code"),
                ],
                'imageOptions' => [
                    "style" => "cursor:pointer;right:0px"
                ]
            ]) ?>
            <?= Html::submitButton(Yii::t("app", "Login"), [
                'class' => 'btn btn-primary block full-width m-b',
                'name' => 'login-button'
            ]) ?>

            <p class="text-muted text-center">
                <a href="<?= Url::to(['admin-user/request-password-reset']) ?>">
                    <small><?= Yii::t('app', 'Forgot password') ?></small>
                </a> |
                <?php
                if (Yii::$app->language == 'en-US') {
                    echo "<a href = " . Url::to(['site/language', 'lang' => 'zh-CN']) . " > 简体中文</a >";
                } else {
                    echo "<a href=" . Url::to(['site/language', 'lang' => 'en-US']) . ">English</a>";
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