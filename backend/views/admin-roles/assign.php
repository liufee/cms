<?php
/**
 * Created by PhpStorm.
 * User: lf
 * Date: 16/4/11
 * Time: 22:34
 */
use feehi\widgets\ActiveForm;
use feehi\widgets\JsBlock;
use feehi\assets\JstreeAsset;

JstreeAsset::register($this);

$this->title = "Assign Permission";
?>
<style>.hide{display: none}</style>
<div class="col-sm-12">
    <div class="ibox">
        <?= $this->render('/widgets/_ibox-title', [
            'buttons' => [
                [
                    'name' => yii::t('app', 'Roles'),
                    'url' => ['index'],
                    'options' => [
                        'class' => 'btn btn-primary btn-xs',
                    ]
                ],
            ]
        ]) ?>
        <div class="ibox-content">
            <div id="permission-tree"></div>
            <div class="hr-line-dashed"></div>
            <?php $form = ActiveForm::begin()?>
            <?= $form->defaultButtons()?>
            <?php ActiveForm::end()?>
        </div>
    </div>
</div>
<?php JsBlock::begin() ?>
    <script>
        $(function() {
            $('#permission-tree').jstree({
                'core' : {
                    'data' : <?=$treeJson?>
                },
                "plugins" : ["checkbox"]
            });

            $("form").on('submit', function (e) {
                e.preventDefault();
                var idArr = $('#permission-tree').jstree().get_checked();
                var ids = idArr.join(',');
                $("form").append("<input type='hidden' name='ids' value='"+ids+"'>");
                $.ajax({
                    url : $('form').attr('action'),
                    method : "post",
                    data : $('form').serialize(),
                }).always(function(){
                    location.reload();
                });
            });
        });
    </script>
<?php JsBlock::end() ?>