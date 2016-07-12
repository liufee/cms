<?php

use yii\helpers\Html;
use feehi\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Options */
/* @var $form ActiveForm */

$this->title = yii::t('app', 'Seo Setting');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5><?=$this->title?> <small></small></h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                    <a class="close-link">
                        <i class="fa fa-times"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'seo_title') ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'seo_keywords') ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'seo_description')->textarea() ?>
                <?= $form->defaultButtons() ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
