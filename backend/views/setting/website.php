<?php

use feehi\widgets\ActiveForm;
use feehi\libs\Constants;

$this->title = yii::t('app', 'Website Setting');
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
                <?= $form->field($model, 'website_title') ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'website_description') ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'website_url') ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'website_email') ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'website_language')->dropDownList(['zh-CN'=>'简体中文','zh-TW'=>'繁体中文','en-US'=>'英语']) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'website_comment')->radioList(Constants::getYesNoItems()) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'website_comment_need_verify')->radioList(Constants::getYesNoItems()) ?>
                <div class="hr-line-dashed"></div>
                <?php
                $temp = \DateTimeZone::listIdentifiers();
                $timezones = [];
                foreach($temp as $v){
                    $timezones[$v] = $v;
                }
                ?>
                <?= $form->field($model, 'website_timezone')->dropDownList($timezones) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'website_icp') ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'website_statics_script')->textarea() ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'website_status')->radioList(Constants::getWebsiteStatusItems()) ?>
                <?= $form->defaultButtons() ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
