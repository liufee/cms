<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-23 15:49
 */

/**
 * @var $this yii\web\View
 * @var $model backend\models\Article
 */

use backend\widgets\ActiveForm;
use common\libs\Constants;
use backend\widgets\Ueditor;

$this->title = "Pages";
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <div class="row form-body form-horizontal m-t">
                    <div class="col-md-12 droppable sortable ui-droppable ui-sortable" style="display: none;">
                    </div>
                    <?php $form = ActiveForm::begin([
                        'options' => [
                            'enctype' => 'multipart/form-data',
                            'class' => 'form-horizontal'
                        ]
                    ]); ?>

                    <!--left start-->
                    <div class="col-md-7 droppable sortable ui-droppable ui-sortable" style="">
                        <?= $form->field($model, 'title')->textInput(); ?>
                        <?= $form->field($model, 'sub_title')->textInput(); ?>
                        <?= $form->field($model, 'summary')->textArea(); ?>
                        <?= $form->field($model, 'content')->widget(Ueditor::className()) ?>
                    </div>
                    <!--left stop -->

                    <!--seo设置start-->
                    <div class="col-md-5 droppable sortable ui-droppable ui-sortable" style="">
                        <div class="ibox-title">
                            <h5><?= yii::t('app', 'Seo Setting') ?></h5>
                        </div>
                        <div class="ibox-content">
                            <?= $form->field($model, 'seo_title', [
                                'size' => 9,
                                'labelOptions' => ['class' => 'col-sm-3']
                            ])->textInput(); ?>
                            <?= $form->field($model, 'seo_keywords', [
                                'size' => 9,
                                'labelOptions' => ['class' => 'col-sm-3']
                            ])->textInput(); ?>
                            <?= $form->field($model, 'seo_description', [
                                'size' => 9,
                                'labelOptions' => ['class' => 'col-sm-3']
                            ])->textInput(); ?>
                        </div>
                    </div>
                    <!--seo设置stop-->


                    <div class="col-md-5 droppable sortable ui-droppable ui-sortable" style="">
                        <div class="ibox-title">
                            <h5><?= yii::t('app', 'Other') ?></h5>
                        </div>
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-sm-4">
                                    <?= $form->field($model, 'status', [
                                        'size' => 7,
                                        'labelOptions' => ['class' => 'col-sm-5 control-label']
                                    ])->dropDownList(Constants::getArticleStatus()); ?>
                                </div>
                                <div class="col-sm-4">
                                    <?= $form->field($model, 'can_comment', [
                                        'size' => 7,
                                        'labelOptions' => ['class' => 'col-sm-5 control-label']
                                    ])->dropDownList(Constants::getYesNoItems()); ?>
                                </div>
                                <div class="col-sm-4">
                                    <?= $form->field($model, 'visibility', [
                                        'size' => 7,
                                        'labelOptions' => ['class' => 'col-sm-5 control-label']
                                    ])->dropDownList(Constants::getArticleVisibility()); ?>
                                </div>
                            </div>
                            <?= $form->field($model, 'tag')->textInput(); ?>
                            <?= $form->field($model, 'sort')->textInput(); ?>

                            <?= $form->defaultButtons(['size' => 12]) ?>
                        </div>
                    </div>
                    <?php $form = ActiveForm::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>