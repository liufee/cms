<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/21
 * Time: 14:35
 */

use feehi\widgets\ActiveForm;
use feehi\libs\Constants;
use yii\helpers\Url;
use backend\models\Menu;

$this->title = "Backend Menus";
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
                <?= $form->field($model, 'method')->dropDownList(Constants::getHttpMethodItems()) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'icon')->textInput(['maxlength' => 64]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'sort')->textInput(['maxlength' => 64]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'is_display')->radioList(Constants::getYesNoItems()) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->defaultButtons() ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>