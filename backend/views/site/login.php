<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

/* @var $this yii\web\View */
/* @var $form backend\widgets\ActiveForm */
/* @var $model \common\models\LoginForm */

use backend\assets\AppAsset;
use yii\helpers\Html;
use backend\widgets\ActiveForm;
use yii\helpers\Url;
use feehi\components\Captcha;

AppAsset::register($this);
$this->title = yii::t('app', 'Login');
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
            div.form-group div.help-block {
                position: absolute;
                left: 305px;
                width: 170px;
                top: 4px;
                text-align: left;
            }

            img#captchaimg {
                cursor: pointer;
                position: absolute;
                top: 0px;
                left: 199px;
            }

            .form-horizontal .form-group {
                width: 300px;
                margin-left: 0px;
            }
        </style>
    </head>
    <body class="gray-bg">
    <style>
        .m-t-md {
            margin-top: 0px
        }
    </style>
    <?php $this->beginBody() ?>
    <div class="middle-box text-center loginscreen  animated fadeInDown">
        <?= $this->render('/widgets/_flash') ?>
        <div>
            <div>

                <h1 class="logo-name">H+</h1>

            </div>
            <h3><?= yii::t('app', 'Welcome to') ?> Feehi CMS</h3>
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
            <?= $form->field($model, 'username', ['template' => "<div style='position:relative'>{input}\n{error}\n{hint}</div>"])
                ->textInput(['autofocus' => true, 'placeholder' => yii::t("app", "Username")]) ?>

            <?= $form->field($model, 'password', ['template' => "<div style='position:relative'>{input}\n{error}\n{hint}</div>"])
                ->passwordInput(['placeholder' => yii::t("app", "Password")]) ?>

            <?php $captcha = Captcha::widget([
                'name' => 'captchaimg',
                'captchaAction' => 'captcha',
                'imageOptions' => [
                    'id' => 'captchaimg',
                    'title' => '换一个',
                    'alt' => '换一个',
                    'style' => 'cursor:pointer;'
                ],
                'template' => '{image}'
            ]);
            ?>
            <?= $form->field($model, 'captcha', ['template' => '<div style="position:relative">{input}' . $captcha . '{error}{hint}</div>'])
                ->textInput(['placeholder' => yii::t("app", "Captcha"), 'style' => 'width:200px']) ?>

            <?= Html::submitButton(yii::t("app", "Login"), [
                'class' => 'btn btn-primary block full-width m-b',
                'name' => 'login-button'
            ]) ?>

            <p class="text-muted text-center"><a href="<?= Url::to(['user/request-password-reset']) ?>">
                    <small><?= yii::t('app', 'Forgot password') ?></small>
                </a> |
                <?php
                if (yii::$app->language == 'en-US') {
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