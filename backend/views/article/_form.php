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
use common\models\Category;
use common\libs\Constants;
use common\widgets\JsBlock;
use backend\widgets\Ueditor;

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
                        <?= $form->field($model, 'thumb')->imgInput(['style' => 'max-width:200px;max-height:200px']); ?>
                        <?= $form->field($model, 'content')->widget(Ueditor::className()) ?>
                    </div>
                    <!--left stop -->

                    <div class="col-md-5 droppable sortable ui-droppable ui-sortable" style="">
                        <div class="ibox-title">
                            <h5><?= yii::t('app', 'Category') ?></h5>
                        </div>
                        <div class="ibox-content">
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-sm-12 col-sm-offset-1">
                                        <div class="form-group col-sm-12 field-article-parent_id">
                                            <div class="col-sm-12 m-l-n">
                                                <?= $form->field($model, 'cid')->label('')->dropDownList(Category::getCategoriesName(), ['multiple'=>''])?>
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
                            <h5><?= yii::t('app', 'Attributes') ?></h5>
                        </div>
                        <div class="ibox-content">
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <?= $form->field($model, 'flag_headline', ['options'=>['tag'=>'span']])->checkbox() ?>
                                        &nbsp;
                                        <?= $form->field($model, 'flag_recommend', ['options'=>['tag'=>'span']])->checkbox() ?>
                                        &nbsp;
                                        <?= $form->field($model, 'flag_slide_show', ['options'=>['tag'=>'span']])->checkbox() ?>
                                        &nbsp;
                                        <?= $form->field($model, 'flag_special_recommend', ['options'=>['tag'=>'span']])->checkbox() ?>
                                        &nbsp;
                                        <?= $form->field($model, 'flag_roll', ['options'=>['tag'=>'span']])->checkbox() ?>
                                        &nbsp;
                                        <?= $form->field($model, 'flag_bold', ['options'=>['tag'=>'span']])->checkbox() ?>
                                        &nbsp;
                                        <?= $form->field($model, 'flag_picture', ['options'=>['tag'=>'span']])->checkbox() ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--属性设置stop-->

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
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                                <a class="close-link">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
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
                            <?php $hide=' hide ';if($model->visibility == Constants::ARTICLE_VISIBILITY_SECRET){$hide='';} ?>
                            <?= $form->field($model, 'password', ['options'=>['class'=>"form-group $hide"]])->textInput(); ?>
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
<?php JsBlock::begin()?>
    <script>
        $(document).ready(function () {
            $("select#article-visibility").change(function () {
                if( $(this).val() == <?=Constants::ARTICLE_VISIBILITY_SECRET?> ){
                    $("div.field-article-password").removeClass('hide');
                }else{
                    $("div.field-article-password").addClass('hide');
                }
            })
        })
    </script>
<?php JsBlock::end()?>