<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-09-04 23:01
 */

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $article frontend\models\Article */
/* @var $model frontend\models\form\ArticlePasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app', $article->title);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-wrap">
    <div class="site-login article-content" style="width:500px;margin: 0 auto;text-align: center">
        <h5><?= Yii::t('frontend', 'Please input the password of article id {id} : {article}', ['article'=>$article->title, 'id'=>$article->id]) ?></h5>
        <style>
            label {
                float: left;
                width: 100px
            }

            div.row input{
                margin-right: 110px;
                width: 220px;
            }
            .help-block-error{
                position: absolute;
                top: 0px;
                right: 0px;
            }
            div.field-loginform-rememberme{
                margin-left: 110px;
            }
        </style>

        <div class="row">
            <div class="col-lg-5">
                <?php $form = ActiveForm::begin(['id' => 'form-login']); ?>

                <?= $form->field($model, 'password', ['template' => "<div style='position:relative'>{label}{input}\n{error}\n{hint}</div>"])->textInput(['autofocus' => true]) ?>

                <div class="form-group" style="margin-right: 50px">
                    <?= Html::submitButton(Yii::t('frontend', 'Go'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
