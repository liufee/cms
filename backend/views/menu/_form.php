<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-21 14:35
 */

/**
 * @var $this yii\web\View
 * @var $model common\models\Menu
 * @var $parentMenuDisabledOptions []
 * @var $menusNameWithPrefixLevelCharacters []
 */

use backend\widgets\ActiveForm;
use common\libs\Constants;

$this->title = "Backend Menus";

$parent_id = yii::$app->getRequest()->get('parent_id', '');
if ($parent_id != '') {
    $model->parent_id = $parent_id;
}
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'parent_id')->label(Yii::t('app', 'Parent Menu Name'))->dropDownList($menusNameWithPrefixLevelCharacters, ['options' => $parentMenuDisabledOptions]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'name')->textInput(['maxlength' => 64]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'is_absolute_url')->radioList(Constants::getYesNoItems()) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'url')->textInput(['maxlength' => 512, 'value'=>$model->convertJSONStringToRelativeUrl()]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'icon')->label(Yii::t('app', 'Icon').' <a href="http://fontawesome.io/icons/" target="_blank">url</a>')->textInput(['maxlength' => 64]) ?>
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