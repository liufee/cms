<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-12-05 13:00
 */

/**
 * @var $this yii\web\View
 * @var $model frontend\models\User
 */
use backend\widgets\ActiveForm;
use common\libs\Constants;
use common\widgets\JsBlock;

$this->title = 'Ad';
?>
<div class="col-sm-12">
    <div class="ibox">
        <?= $this->render('/widgets/_ibox-title') ?>
        <div class="ibox-content">

            <?php $form = ActiveForm::begin([
                'options' => [
                    'enctype' => 'multipart/form-data',
                    'class' => 'form-horizontal'
                ]
            ]); ?>
            <?= $form->field($model, 'name')->textInput(); ?>
            <div class="hr-line-dashed"></div>
            <?= $form->field($model, 'tips')->textInput(); ?>
            <div class="hr-line-dashed"></div>
            <?= $form->field($model, 'input_type')->dropDownList(Constants::getAdTypeItems()); ?>
            <div class="hr-line-dashed"></div>
            <?php
            foreach (Constants::getAdTypeItems() as $type => $name){
                $options = ['options'=>['class'=>"form-group input_$type"]];
                switch ($type){
                    case Constants::AD_TEXT:
                        echo $form->field($model, "ad", $options)
                            ->textarea();
                        break;
                    case  Constants::AD_IMG:
                        echo $form->field($model,"ad", $options)
                            ->imgInput( ['style'=>"max-width:300px;max-height:200px"] );
                        break;
                    case Constants::AD_VIDEO:
                        echo $form->field($model, "ad", $options)
                            ->fileInput();
                        break;
                }
            }
            ?>
            <div class="hr-line-dashed"></div>
            <?= $form->field($model, 'link')->textInput(); ?>
            <div class="hr-line-dashed"></div>
            <?= $form->field($model, 'desc')->textInput(); ?>
            <div class="hr-line-dashed"></div>
            <?= $form->field($model, 'target')->radioList(Constants::getTargetOpenMethod()); ?>
            <div class="hr-line-dashed"></div>
            <?= $form->field($model, 'sort')->textInput(); ?>
            <div class="hr-line-dashed"></div>
            <?= $form->field($model, 'autoload')->radioList(Constants::getStatusItems()); ?>
            <div class="hr-line-dashed"></div>
            <?= $form->defaultButtons() ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php JsBlock::begin() ?>
<script>
    $(document).ready(function () {
        var defaultType = $("select#adform-input_type :selected").val();
        $(".form-group.field-adform-ad").not(".input_"+defaultType).hide().find("input[name=AdForm\\[ad\\]], textarea[name=AdForm\\[ad\\]]").attr('disabled', true);
        $("select#adform-input_type").change(function () {
            var type = parseInt( $(this).val() );
            $(".form-group.field-adform-ad").hide().find("input[name=AdForm\\[ad\\]], textarea[name=AdForm\\[ad\\]]").attr('disabled', true);
            $(".form-group.field-adform-ad.input_"+type).show().attr('name', 'AdForm[value]').find("input[name=AdForm\\[ad\\]], textarea[name=AdForm\\[ad\\]]").attr('disabled', false);
        })
    })
</script>
<?php JsBlock::end() ?>
