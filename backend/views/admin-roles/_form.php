<?php
/**
 * Created by PhpStorm.
 * User: lf
 * Date: 16/4/11
 * Time: 22:03
 */
use feehi\widgets\ActiveForm;

$this->title = "Roles";
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?php $form = ActiveForm::begin(); ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'role_name')->textInput(['maxlength' => 64]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'remark')->textInput(['maxlength' => 64]) ?>
                <?= $form->defaultButtons() ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
