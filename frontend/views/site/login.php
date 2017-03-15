<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = yii::t('app', 'Login');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-wrap">
    <div class="site-login article-content" style="width:500px;margin: 0 auto;text-align: center">
        <h1><?= Html::encode($this->title) ?></h1>
        <style>
            label {
                display: inline
            }
        </style>
        <div class="row">
            <div class="col-lg-5">
                <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'password')->passwordInput() ?>

                <?= $form->field($model, 'rememberMe')->checkbox() ?>

                <div style="color:#999;margin:1em 0">
                    <?= yii::t('frontend', 'If you forgot your password you can') ?> <?= Html::a(yii::t('frontend', 'reset it'), ['site/request-password-reset']) ?>
                    .
                </div>

                <div class="form-group">
                    <?= Html::submitButton(yii::t('app', 'Login'), [
                        'class' => 'btn btn-primary',
                        'name' => 'login-button'
                    ]) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
