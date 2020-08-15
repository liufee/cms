<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model frontend\models\form\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app', 'Reset Password') . '-' . Yii::$app->feehi->website_title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-wrap">
    <div class="fill">
        <h1><?= Html::encode($this->title) ?></h1>
        <p><?= Yii::t('app', 'Please choose your new password') ?>:</p>

        <div class="row">
            <ul class="formInput">
                <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

                <?= $form->field($model, 'password', ['template' => "<li class='item'>{label}{input}\n{error}\n{hint}</li>"])->textInput(['autofocus' => true]) ?>

                <div class="submitButton"">
                    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </>
        </div>
    </div>
</div>