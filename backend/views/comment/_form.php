<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-23 15:49
 */

/**
 * @var $this yii\web\View
 * @var $model backend\models\Comment
 */

use backend\widgets\ActiveForm;
use common\libs\Constants;

$this->title = "Comments";
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <?=$this->render('/widgets/_ibox-title')?>
            <div class="ibox-content">
                <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'nickname') ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'content')->textarea() ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'website_url') ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'ip') ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'status')->radioList(Constants::getCommentStatusItems()) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->defaultButtons() ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>