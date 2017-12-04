<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-12-03 23:08
 */


/**
 * @var $this yii\web\View
 * @var $model backend\models\form\BannerForm
 */

use backend\widgets\ActiveForm;
use common\libs\Constants;

?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?php $form = ActiveForm::begin(); ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'img')->imgInput() ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'link')->textInput() ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'desc')->textInput() ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'target')->radioList(Constants::getTargetOpenMethod()) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'sort')->textInput() ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'status')->radioList(Constants::getStatusItems()) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->defaultButtons() ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>