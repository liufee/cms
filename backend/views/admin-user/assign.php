<?php
/**
 * Created by PhpStorm.
 * User: lf
 * Date: 16/4/11
 * Time: 22:34
 */
use yii\helpers\Url;
use feehi\widgets\ActiveForm;
use yii\bootstrap\Alert;
?>
<div class="col-sm-12">
    <div class="ibox">
        <div class="ibox-title">
            <h5><?=yii::t('app', 'Assign Roles')?></h5>
            <div class="ibox-tools">
                <a href="<?=Url::toRoute('index')?>" class="btn btn-primary btn-xs"><?=yii::t('app', 'Administrators')?></a>
            </div>
        </div>
        <div class="ibox-content">

            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'role_id', ['labelOptions'=>['style'=>'display:none']])->radioList($roles) ?>
            <div class="hr-line-dashed"></div>
            <?= $form->defaultButtons() ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
