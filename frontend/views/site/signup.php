<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model frontend\models\form\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('frontend', 'Sign up') . '-' . Yii::$app->feehi->website_title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-wrap">
    <div class="fill" style="width:500px; margin: 0 auto">
        <h1><?= Html::encode($this->title) ?></h1>
        <p><?= Yii::t('frontend', 'Please fill out the following fields to signup') ?>:</p>

        <div class="row">
            <ul class="formInput">
                <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

                <?= $form->field($model, 'username', ['template' => "<li class='item'>{label}{input}\n{error}\n{hint}</li>", 'options' => ['class'=>'row'], 'labelOptions'=>['class'=>'col-sm-4'], 'inputOptions'=>['class'=>'col-sm-8']])->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'email', ['template' => "<li class='item'>{label}{input}\n{error}\n{hint}</li>"])->textInput() ?>

                <?= $form->field($model, 'password', ['template' => "<li class='item'>{label}{input}\n{error}\n{hint}</li>"])->passwordInput() ?>

                <div class="submitButton">
                    <?= Html::submitButton(Yii::t('frontend', 'SignUp'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </ul>
        </div>
    </div>
</div>
