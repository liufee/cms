<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/23
 * Time: 15:49
 */
use backend\widgets\ActiveForm;
use common\models\Category;
use feehi\widgets\Ueditor;
use feehi\libs\Constants;
use yii\helpers\Html;
use yii\bootstrap\Alert;
use yii\helpers\Url;

$this->title = "Comments";
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5><?=$this->title?> <small></small></h5>
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
                <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'nickname') ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'content')->textarea() ?>
                <?= $form->field($model, 'status')->radioList(Constants::getCommentStatusItems()) ?>
                <?= $form->defaultButtons() ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>