<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

/* @var $this yii\web\View */
/* @var $form \frontend\widgets\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use frontend\widgets\ActiveForm;

$this->registerMetaTag(['keywords' => yii::$app->feehi->seo_keywords]);
$this->registerMetaTag(['description' => yii::$app->feehi->seo_description]);

$this->title = yii::t('app', 'Login') . '-' . yii::$app->feehi->website_title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-wrap">
    <div class="site-login article-content" style="width:500px;margin: 0 auto;text-align: center">
        <h1><?= Html::encode($this->title) ?></h1>
        <style>
            label {
                float: left;
                width: 100px
            }

            div.row input{
                margin-right: 110px;
                width: 220px;
            }

            div.field-loginform-rememberme{
                margin-left: 110px;
            }
        </style>

        <div class="row">
            <div class="col-lg-5">
                <?php $form = ActiveForm::begin(['id' => 'form-login']); ?>

                <?= $form->field($model, 'username', ['template' => "<div style='position:relative'>{label}{input}\n{error}\n{hint}</div>"])->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'password', ['template' => "<div style='position:relative'>{label}{input}\n{error}\n{hint}</div>"])->passwordInput() ?>

                <?= $form->field($model, 'rememberMe')->checkbox()?>

                <div style="color:#999;margin-right: 120px;">
                    <?= yii::t('frontend', 'If you forgot your password you can') ?> <?= Html::a(yii::t('frontend', 'reset it'), ['site/request-password-reset']) ?>
                </div>

                <div class="form-group" style="margin-right: 50px">
                    <?= Html::submitButton(yii::t('frontend', 'Login'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
