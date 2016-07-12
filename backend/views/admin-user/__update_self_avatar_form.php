<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/31
 * Time: 17:08
 */
use yii\helpers\Url;
use feehi\widgets\ActiveForm;
?>
<div class="col-sm-12">
    <div class="ibox">
        <div class="ibox-title">
            <h5>修改资料</h5>
            <div class="ibox-tools">
                <a href="<?=Url::toRoute('main')?>" class="btn btn-primary btn-xs">首页</a>
            </div>
        </div>
        <div class="ibox-content">

            <?php $form = ActiveForm::begin(['options'=>['enctype'=>'multipart/form-data','class'=>'form-horizontal']]); ?>
            <?= $form->field($model, 'email')->textInput(['maxlength' => 512]) ?>
            <div class="hr-line-dashed"></div>
            <?= $form->field($model, 'avatar')->fileInput(['maxlength' => 64]) ?>
            <div class="hr-line-dashed"></div>
            <?= $form->field($model, 'old_password')->textInput(['maxlength' => 64]) ?>
            <div class="hr-line-dashed"></div>
            <?= $form->field($model, 'password')->textInput(['maxlength' => 64]) ?>
            <div class="hr-line-dashed"></div>
            <?= $form->field($model, 'repassword')->textInput(['maxlength' => 64]) ?>
            <div class="hr-line-dashed"></div>
            <?= $form->defaultButtons() ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

