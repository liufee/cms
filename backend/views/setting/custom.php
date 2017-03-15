<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

/** @var $this yii\web\View
 * @var $model common\models\Options
 * @var $form ActiveForm
 */

use backend\widgets\ActiveForm;
use common\widgets\JsBlock;
use common\libs\Constants;
use yii\helpers\Url;
use backend\widgets\Ueditor;

$this->title = yii::t('app', 'Custom Setting');
?>
<div class="row" xmlns="http://www.w3.org/1999/html">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5><?= $this->title ?>
                    <small></small>
                </h5>
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
                <?php
                $form = ActiveForm::begin();
                foreach ($settings as $index => $setting) {
                    $deleteUrl = Url::to(['delete', 'id' => $setting->id]);
                    $editUrl = Url::to(['custom-update', 'id' => $setting->id]);
                    $template = "{label}\n<div class=\"col-sm-8\">{input}\n{error}</div>\n{hint}<div class='col-sm-2'><span class='tips'><i class='fa fa-info-circle'></i> {$setting->tips}  <a class='btn-delete' href='{$deleteUrl}' title='' data-confirm='' data-method='' data-pjax='1'><i style='float: right' class='fa fa-trash-o'></i></a><a href='{$editUrl}' class='btn_edit' title='编辑' data-pjax=''><i style='float: right;margin-right: 10px;' class='fa fa-pencil'></i></a> </span></div>";
                    if ($setting->input_type == Constants::INPUT_UEDITOR) {
                        echo $form->field($setting, "[$index]value", ['template' => $template])
                            ->label($setting->name)
                            ->widget(Ueditor::className(), ['name' => 'value' . $index]);

                    } else {
                        if ($setting->input_type == Constants::INPUT_INPUT) {
                            echo $form->field($setting, "[$index]value", ['template' => $template])
                                ->label($setting->name)
                                ->textInput();
                        } else {
                            echo $form->field($setting, "[$index]value", ['template' => $template])
                                ->label($setting->name)
                                ->textarea();
                        }
                    }

                    ?>
                    <div class="hr-line-dashed"></div>
                    <?php
                }
                ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-8">
                        <a style="float:right;" type="button" class="btn btn-outline btn-default"
                           id="add"><?= yii::t('app', 'Add') ?></a>
                    </div>
                </div>
                <?= $form->defaultButtons() ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<?php JsBlock::begin() ?>
<script>
    $(document).ready(function () {
        $('#add').click(function () {
            layer.open({
                type: 1,
                title: '<?=yii::t('app', 'Add')?>',
                maxmin: true,
                shadeClose: true, //点击遮罩关闭层
                area: ['70%', '80%'],
                content: $("#addForm").html(),
            });
            $("form#w1").bind('submit', function () {
                /*
                 var index = parent.layer.load(1, {
                 shade: [0.1,'red'] //0.1透明度的白色背景
                 });*/
                var $form = $(this);
                $.ajax({
                    url: $form.attr('action'),
                    type: "post",
                    data: $form.serialize(),
                    success: function (data) {
                        layer.msg(data.err_msg);
                    }
                }).always(function () {
                    //clearTimeout(index);
                });
                return false;
            });
        });
        $("a.btn_edit").click(function () {
            var name = $(this).parents("div.form-group").children("label").html();
            $.ajax({
                url: $(this).attr('href'),
                success: function (data) {
                    layer.open({
                        type: 1,
                        title: '<?=yii::t('app', 'Update')?> ' + name,
                        maxmin: true,
                        shadeClose: true, //点击遮罩关闭层
                        area: ['70%', '80%'],
                        content: data,
                    });
                    $("form[name=edit]").bind('submit', function () {
                        /*
                         var index = parent.layer.load(1, {
                         shade: [0.1,'red'] //0.1透明度的白色背景
                         });*/
                        var $form = $(this);
                        $.ajax({
                            url: $form.attr('action'),
                            type: "post",
                            data: $form.serialize(),
                            success: function (data) {
                                layer.msg(data.err_msg);
                            }
                        }).always(function () {
                            //clearTimeout(index);
                        });
                        return false;
                    });
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    alert("ajax错误," + textStatus + ' : ' + errorThrown);
                },
                complete: function (XMLHttpRequest, textStatus) {
                }
            });
            return false;
        })
    });
</script>
<?php JsBlock::end() ?>
<div class="hide" id="addForm">
    <div class="ibox-content">
        <?php
        ActiveForm::begin(['action' => \yii\helpers\Url::to(['setting/custom-create'])]);
        echo $form->field($model, 'name')->textInput();
        echo $form->field($model, 'input_type')->dropDownList(Constants::getInputTypeItems());
        echo $form->field($model, 'tips')->textInput();
        echo $form->field($model, 'autoload')->dropDownList(Constants::getYesNoItems());
        echo $form->field($model, 'sort')->textInput();
        echo $form->defaultButtons();
        ActiveForm::end();
        ?>
    </div>
</div>
