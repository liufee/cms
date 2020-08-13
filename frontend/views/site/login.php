<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

/* @var $this yii\web\View */
/* @var $form \yii\bootstrap\ActiveForm*/
/* @var $model \frontend\models\form\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app', 'Login') . '-' . Yii::$app->feehi->website_title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-wrap">
    <div class="site-login article-content" style="width:500px;margin: 0 auto;text-align: center">
        <h1><?= Html::encode($this->title) ?></h1>
        <style>
            label {
                float: left;
                width: 103px
            }

            div.row input{
                margin-right: 110px;
            }
        </style>

        <div class="row">
            <div class="col-lg-5">
                <?php $form = ActiveForm::begin(['id' => 'form-login']); ?>

                <?= $form->field($model, 'username', ['template' => "{label}{input}\n{error}\n{hint}", 'labelOptions'=>['class'=>'col-sm-4 control-label'], 'options'=>['class'=>'row'], 'inputOptions'=>['class'=>'col-sm-8']])->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'password', ['template' => "{label}{input}\n{error}\n{hint}", 'labelOptions'=>['class'=>'col-sm-4 control-label'], 'options'=>['class'=>'row'], 'inputOptions'=>['class'=>'col-sm-8']])->passwordInput() ?>

                <div class="form-group">
                    <?= $form->field($model, 'rememberMe')->checkbox()?>
                    <?= Yii::t('frontend', 'If you forgot your password you can') ?> <?= Html::a(Yii::t('frontend', 'reset it'), ['site/request-password-reset']) ?>
                </div>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('frontend', 'Login'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
