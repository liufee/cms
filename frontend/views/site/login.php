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
    <div class="fill">
        <h1><?= Html::encode($this->title) ?></h1>
        <div class="marginTop">
            <?php $form = ActiveForm::begin(['id' => 'form-login']); ?>
            <ul class="formInput">
                <?= $form->field($model, 'username', ['template' => "<li class='item'>{label}{input}\n{error}\n{hint}</li>", 'labelOptions'=>['class'=>'col-sm-4 control-label'], 'options'=>['class'=>'row'], 'inputOptions'=>['class'=>'col-sm-8']])->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'password', ['template' => "<li class='item'>{label}{input}\n{error}\n{hint}</li>", 'labelOptions'=>['class'=>'col-sm-4 control-label'], 'options'=>['class'=>'row'], 'inputOptions'=>['class'=>'col-sm-8']])->passwordInput() ?>
            </ul>
            <div style="clear:both;"></div>
            <div class="form-group" style="text-align: center">
                <?= $form->field($model, 'rememberMe', ['template'=>'{label}{input}'])->error(false)->checkbox()?>
                <span style="display: block;text-align: right"><?= Yii::t('frontend', 'If you forgot your password you can') ?> <?= Html::a(Yii::t('frontend', 'reset it'), ['site/request-password-reset']) ?></span>

            </div>

            <div class="submitButton">
                <?= Html::submitButton(Yii::t('frontend', 'Login'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
