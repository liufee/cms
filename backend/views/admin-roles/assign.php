<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-11 22:34
 */

use backend\widgets\ActiveForm;
use common\widgets\JsBlock;
use backend\assets\JstreeAsset;

JstreeAsset::register($this);

$this->title = "Assign Permission";
?>
    <style>
        .hide {
            display: none
        }
    </style>
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <div class="row text-center"><span style="font-weight: bold;"><?= $role_name ?></span></div>
                <div id="permission-tree"></div>
                <div class="hr-line-dashed"></div>
                <?php $form = ActiveForm::begin() ?>
                <?= $form->defaultButtons() ?>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
<?php JsBlock::begin() ?>
    <script>
        $(function () {
            $('#permission-tree').jstree({
                'core': {
                    'data': <?=$treeJson?>
                },
                "plugins": ["checkbox"]
            });

            $("form").submit(function () {
                var idArr = $('#permission-tree').jstree().get_checked();
                var ids = idArr.join(',');
                $("form").append("<input type='hidden' name='ids' value='" + ids + "'>");
            });
        });
    </script>
<?php JsBlock::end() ?>