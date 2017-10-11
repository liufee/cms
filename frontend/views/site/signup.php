<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

/* @var $this yii\web\View */
/* @var $form frontend\widgets\ActiveForm */
/* @var $model frontend\models\form\SignupForm */

use yii\helpers\Html;
use frontend\widgets\ActiveForm;

$this->title = yii::t('frontend', 'Sign up') . '-' . yii::$app->feehi->website_title;
$this->params['breadcrumbs'][] = $this->title;

$this->registerMetaTag(['keywords' => yii::$app->feehi->seo_keywords]);
$this->registerMetaTag(['description' => yii::$app->feehi->seo_description]);
?>
<div class="content-wrap">
    <div class="site-signup article-content" style="width:500px; margin: 0 auto">
        <h1><?= Html::encode($this->title) ?></h1>
        <style>
            label {
                float: left;
                width: 100px
            }
        </style>
        <p><?= yii::t('frontend', 'Please fill out the following fields to signup') ?>:</p>

        <div class="row">
            <div class="col-lg-5">
                <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

                <?= $form->field($model, 'username', ['template' => "<div style='position:relative'>{label}{input}\n{error}\n{hint}</div>"])->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'email', ['template' => "<div style='position:relative'>{label}{input}\n{error}\n{hint}</div>"])->textInput() ?>

                <?= $form->field($model, 'password', ['template' => "<div style='position:relative'>{label}{input}\n{error}\n{hint}</div>"])->passwordInput() ?>

                <div class="form-group" style="margin-left: 180px">
                    <?= Html::submitButton(yii::t('frontend', 'Signup'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
