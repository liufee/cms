<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/23
 * Time: 15:49
 */
use feehi\widgets\ActiveForm;
use common\models\Category;
use feehi\widgets\Ueditor;
use feehi\libs\Constants;
use yii\helpers\Html;
use yii\bootstrap\Alert;
use yii\helpers\Url;

$this->title = "Articles";
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <div class="row form-body form-horizontal m-t">
                    <div class="col-md-12 droppable sortable ui-droppable ui-sortable" style="display: none;">
                    </div>
                    <?php $form = ActiveForm::begin(['options'=>['enctype'=>'multipart/form-data','class'=>'form-horizontal']]); ?>

                    <!--left start-->
                    <div class="col-md-7 droppable sortable ui-droppable ui-sortable" style="">
                        <?= $form->field($model, 'title')->textInput();?>
                        <?= $form->field($model, 'sub_title')->textInput();?>
                        <?= $form->field($model, 'sumary')->textArea();?>
                        <?= $form->field($model, 'thumb')->imgInput(['width'=>'200px']);?>
                        <?= $form->field($model, 'content')->ueditor()?>
                    </div>
                    <!--left stop -->

                    <div class="col-md-5 droppable sortable ui-droppable ui-sortable" style="">
                        <div class="ibox-title">
                            <h5>文章分类</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                                <a class="dropdown-toggle" data-toggle="dropdown" href="calendar.html#">
                                    <i class="fa fa-wrench"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-user">
                                    <li><a href="calendar.html#">选项1</a>
                                    </li>
                                    <li><a href="calendar.html#">选项2</a>
                                    </li>
                                </ul>
                                <a class="close-link">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-sm-12 col-sm-offset-1">
                                        <div class="form-group col-sm-12 field-article-parent_id">
                                            <div class="col-sm-12 m-l-n">
                                                <select name="Article[cid]" class="form-control" multiple="">
                                                    <?=Category::getOptions($model->cid)?>
                                                </select>
                                            </div>
                                            <div class="help-block m-b-none"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--属性设置start-->
                    <div class="col-md-5 droppable sortable ui-droppable ui-sortable" style="">
                        <div class="ibox-title">
                            <h5>属性设置</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                                <a class="dropdown-toggle" data-toggle="dropdown" href="calendar.html#">
                                    <i class="fa fa-wrench"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-user">
                                    <li><a href="calendar.html#">选项1</a>
                                    </li>
                                    <li><a href="calendar.html#">选项2</a>
                                    </li>
                                </ul>
                                <a class="close-link">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <?=Html::activeCheckbox($model, 'flag_headline', []) ?>
                                        &nbsp;
                                        <?=Html::activeCheckbox($model, 'flag_recommend', []) ?>
                                        &nbsp;
                                        <?=Html::activeCheckbox($model, 'flag_slide_show', []) ?>
                                        &nbsp;
                                        <?=Html::activeCheckbox($model, 'flag_special_recommend', []) ?>
                                        &nbsp;
                                        <?=Html::activeCheckbox($model, 'flag_roll', []) ?>
                                        &nbsp;
                                        <?=Html::activeCheckbox($model, 'flag_bold', []) ?>
                                        &nbsp;
                                        <?=Html::activeCheckbox($model, 'flag_picture', []) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--属性设置stop-->

                    <!--seo设置start-->
                    <div class="col-md-5 droppable sortable ui-droppable ui-sortable" style="">
                        <div class="ibox-title">
                            <h5>seo设置</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                                <a class="dropdown-toggle" data-toggle="dropdown" href="calendar.html#">
                                    <i class="fa fa-wrench"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-user">
                                    <li><a href="calendar.html#">选项1</a>
                                    </li>
                                    <li><a href="calendar.html#">选项2</a>
                                    </li>
                                </ul>
                                <a class="close-link">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <?= $form->field($model, 'seo_title', ['size'=>9,'labelOptions'=>['class'=>'col-sm-3']])->textInput();?>
                            <?= $form->field($model, 'seo_keywords', ['size'=>9,'labelOptions'=>['class'=>'col-sm-3']])->textInput();?>
                            <?= $form->field($model, 'seo_description', ['size'=>9,'labelOptions'=>['class'=>'col-sm-3']])->textInput();?>
                        </div>
                    </div>
                    <!--seo设置stop-->


                    <div class="col-md-5 droppable sortable ui-droppable ui-sortable" style="">
                        <div class="ibox-title">
                            <h5>其他</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                                <a class="dropdown-toggle" data-toggle="dropdown" href="calendar.html#">
                                    <i class="fa fa-wrench"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-user">
                                    <li><a href="calendar.html#">选项1</a>
                                    </li>
                                    <li><a href="calendar.html#">选项2</a>
                                    </li>
                                </ul>
                                <a class="close-link">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-sm-4">
                                    <?= $form->field($model, 'status', ['size'=>7, 'labelOptions'=>['class'=>'col-sm-5 control-label']])->dropDownList(Constants::getArticleStatus());?>
                                </div>
                                <div class="col-sm-4">
                                    <?= $form->field($model, 'can_comment', ['size'=>7, 'labelOptions'=>['class'=>'col-sm-5 control-label']])->dropDownList(Constants::getYesNoItems());?>
                                </div>
                                <div class="col-sm-4">
                                    <?= $form->field($model, 'visibility', ['size'=>7, 'labelOptions'=>['class'=>'col-sm-5 control-label']])->dropDownList(Constants::getArticleVisibility());?>
                                </div>
                            </div>
                            <?= $form->field($model, 'tag')->textInput();?>
                            <?= $form->field($model, 'sort')->textInput();?>

                            <?= $form->defaultButtons(['size'=>12]) ?>
                        </div>
                    </div>
                    <?php $form = ActiveForm::end()?>
                </div>
             </div>
        </div>
    </div>
</div>