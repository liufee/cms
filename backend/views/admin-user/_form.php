<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/25
 * Time: 11:15
 */

use feehi\widgets\ActiveForm;

$this->title = "Admin";
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?php $form = ActiveForm::begin(['options'=>['enctype'=>'multipart/form-data','class'=>'form-horizontal']]); ?>
                <?= $form->field($model, 'username')->textInput(['maxlength' => 64]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'avatar')->imgInput(['width'=>'100px', 'baseUrl'=>yii::$app->params['admin']['url']]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'email')->textInput(['maxlength' => 64]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'password')->passwordInput(['maxlength' => 512]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($rolesModel, 'role_id', ['labelOptions' => ['label' => yii::t('app', 'Roles'), 'class'=>"col-sm-2 control-label"]])->radioList($roles) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->defaultButtons() ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
