<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2018-09-16 20:32
 */
use backend\widgets\ActiveForm;
use common\libs\Constants;

/**
 * @var $formType string
 * @var $model \common\models\Options
 */
?>
<div class="ibox-content">
    <?php $form = ActiveForm::begin(['options' => ['name' => 'custom']]);?>
    <?= $form->field($model, 'name')->textInput();?>
    <?= $form->field($model, 'input_type')->dropDownList(Constants::getInputTypeItems());?>
    <?= $form->field($model, 'tips')->textInput();?>
    <?= $form->field($model, 'autoload')->dropDownList(Constants::getYesNoItems());?>
    <?= $form->field($model, 'value')->textInput();?>
    <?= $form->field($model, 'sort')->textInput();?>
    <?= $form->defaultButtons();?>
    <?php ActiveForm::end();?>
</div>