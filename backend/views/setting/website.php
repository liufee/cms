<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

/**
 * @var $this \yii\web\View
 * @var $model common\models\Options
 */

use backend\widgets\ActiveForm;
use common\libs\Constants;

$this->title = Yii::t('app', 'Website Setting');
$this->params['breadcrumbs'][] = Yii::t('app', 'Website Setting');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <?=$this->render('/widgets/_ibox-title')?>
            <div class="ibox-content">
                <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'website_title') ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'website_url') ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'seo_keywords') ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'seo_description')->textarea() ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'website_language')->dropDownList([
                    'zh-CN' => '简体中文',
                    'zh-TW' => '繁体中文',
                    'en-US' => 'English'
                ]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'website_comment')->radioList(Constants::getYesNoItems()) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'website_comment_need_verify')->radioList(Constants::getYesNoItems()) ?>
                <div class="hr-line-dashed"></div>
                <?php
                $temp = \DateTimeZone::listIdentifiers();
                $timezones = [];
                foreach ($temp as $v) {
                    $timezones[$v] = $v;
                }
                ?>
                <?= $form->field($model, 'website_timezone')->chosenSelect($timezones) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'website_icp') ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'website_statics_script')->textarea() ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'website_status')->radioList(Constants::getWebsiteStatusItems()) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->defaultButtons() ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
