<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model frontend\models\form\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app', 'Request password reset') . '-' . Yii::$app->feehi->website_title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-wrap">
    <div class="fill">
        <h1><?= Html::encode($this->title) ?></h1>
        <p><?= Yii::t('app', 'Please fill out your email. A link to reset password will be sent there.') ?></p>

        <div class="row">
            <ul class="formInput">
                <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

                <?= $form->field($model, 'email', ['template' => "<li class='item'>{label}{input}\n{error}\n{hint}</li>", 'labelOptions'=>['class'=>'col-sm-4 control-label'], 'options'=>['class'=>'row']])->textInput(['autofocus' => true]) ?>

                <div class="submitButton">
                    <?= Html::submitButton(Yii::t('app', 'Send'), ['class' => 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </ul>
        </div>
    </div>
</div>