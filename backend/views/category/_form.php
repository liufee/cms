<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/21
 * Time: 14:35
 */

use feehi\widgets\ActiveForm;
use common\models\Category;

$this->title = "Category";
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?php $form = ActiveForm::begin(); ?>
                <div class="form-group field-category-parent_id">
                    <label for="category-parent_id" class="col-sm-2 control-label">父分类</label>
                    <div class="col-sm-10 m-l-n" style="margin-left: 0px">
                        <select name="Category[parent_id]" class="form-control" multiple="">
                            <?=Category::getOptions($model->parent_id)?>
                        </select>
                    </div>
                    <div class="help-block m-b-none"></div>
                </div>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'name')->textInput(['maxlength' => 64]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'sort')->textInput(['maxlength' => 512]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'remark')->textInput(['maxlength' => 64]) ?>
                <?= $form->defaultButtons() ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>