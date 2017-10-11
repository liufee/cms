<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

/* @var $this yii\web\View */
/* @var $form frontend\widgets\ActiveForm */
/* @var $model \frontend\models\form\ResetPasswordForm */

use yii\helpers\Html;
use frontend\widgets\ActiveForm;

$this->title = yii::t('app', 'Reset Password') . '-' . yii::$app->feehi->website_title;
$this->params['breadcrumbs'][] = $this->title;

$this->registerMetaTag(['keywords' => yii::$app->feehi->seo_keywords]);
$this->registerMetaTag(['description' => yii::$app->feehi->seo_description]);
?>
<div class="content-wrap">
    <div class="site-signup article-content" style="width:100%; margin: 0 auto">
        <h1><?= Html::encode($this->title) ?></h1>
        <style>
            label {
                float: left;
                width: 60px;
            }
            p.help-block.help-block-error{
                left: 300px;
                width: 240px;
            }
        </style>
        <p><?= yii::t('app', 'Please choose your new password') ?>:</p>

        <div class="row">
            <div class="col-lg-5">
                <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

                <?= $form->field($model, 'password', ['template' => "<div style='position:relative'>{label}{input}\n{error}\n{hint}</div>"])->textInput(['autofocus' => true]) ?>

                <div class="form-group">
                    <?= Html::submitButton(yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>