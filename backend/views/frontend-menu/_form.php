<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/21
 * Time: 14:35
 */

use feehi\widgets\ActiveForm;
use feehi\libs\Constants;
use frontend\models\Menu;

$this->title = "Frontend Menus";
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'parent_id')->dropDownList(Menu::getParentMenu())?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'name')->textInput(['maxlength' => 64]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'url')->textInput(['maxlength' => 512]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'icon')->textInput(['maxlength' => 64]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'sort')->textInput(['maxlength' => 64]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'target')->radioList(Constants::getTargetOpenMethod()) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'is_absolute_url')->radioList(Constants::getYesNoItems()) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'is_display')->radioList(Constants::getStatusItems()) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->defaultButtons() ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>