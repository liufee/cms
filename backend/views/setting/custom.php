<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

/**
 * @var $this yii\web\View
 * @var $model common\models\Options
 * @var $settings array
 */

use backend\widgets\ActiveForm;
use common\widgets\JsBlock;
use common\libs\Constants;
use yii\helpers\Url;
use backend\widgets\Ueditor;

$this->title = Yii::t('app', 'Custom Setting');
$this->params['breadcrumbs'][] = Yii::t('app', 'Custom Setting');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <?=$this->render('/widgets/_ibox-title')?>
            <div class="ibox-content">
                <?php
                $form = ActiveForm::begin();
                foreach ($settings as $index => $setting) {
                    $deleteUrl = Url::to(['custom-delete', 'id' => $setting->id]);
                    $editUrl = Url::to(['custom-update', 'id' => $setting->id]);
                    $template = "{label}\n<div class=\"col-sm-8\">{input}\n{error}</div>\n{hint}<div class='col-sm-2'><span class='tips'><i class='fa fa-info-circle'></i> {$setting->tips}  <a class='btn-delete' href='{$deleteUrl}' data-confirm='" . Yii::t('app', 'Are you sure you want to delete this item?') . "' title='' data-method='' data-pjax='1'><i style='float: right' class='fa fa-trash-o'></i></a><a href='{$editUrl}' class='btn_edit' title='编辑' data-pjax=''><i style='float: right;margin-right: 10px;' class='fa fa-edit'></i></a> </span></div>";
                    if ($setting->input_type == Constants::INPUT_UEDITOR) {
                        echo $form->field($setting, "[$index]value", ['template' => $template])
                            ->label($setting->name)
                            ->widget(Ueditor::className(), ['name' => 'value' . $index]);

                    } else if($setting->input_type == Constants::INPUT_IMG){
                        echo $form->field($setting,"[$index]value", ['template'=>"{label}\n<div class=\"col-sm-8 image\">{input}<div style='position: relative'>{img}{actions}</div>\n{error}</div>\n{hint}<div class='col-sm-2'><span class='tips'><i class='fa fa-info-circle'></i> {$setting->tips}  <a class='btn-delete' href='{$deleteUrl}' title='' data-confirm='' data-method='' data-pjax='1'><i style='float: right' class='fa fa-trash-o'></i></a><a href='{$editUrl}' class='btn_edit' title='编辑' data-pjax=''><i style='float: right;margin-right: 10px;' class='fa fa-edit'></i></a> </span></div>"])
                            ->label($setting->name)
                            ->imgInput( ['value' => $setting->value, 'style'=>"max-width:300px;max-height:200px"] );
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
                           id="add"><?= Yii::t('app', 'Add') ?></a>
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
            $.ajax({
                url: "<?=Url::toRoute("setting/custom-create")?>",
                success: function (data) {
                    layer.open({
                        type: 1,
                        title: "<?=Yii::t('app', 'Create')?>",
                        maxmin: true,
                        shadeClose: true, //点击遮罩关闭层
                        area: ['70%', '80%'],
                        content: data,
                    });
                    $("form[name=custom]").bind('submit', function () {
                        var $form = $(this);
                        $.ajax({
                            url: $form.attr('action'),
                            type: "post",
                            data: $form.serialize(),
                            beforeSend: function () {
                                layer.load(2,{
                                    shade: [0.1,'#fff'] //0.1透明度的白色背景
                                });
                            },
                            success: function (data) {
                                location.href = "<?=Url::toRoute(['custom'])?>";
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                layer.alert(jqXHR.responseJSON.message, {
                                    title:tips.error,
                                    btn: [tips.ok],
                                    icon: 2,
                                    skin: 'layer-ext-moon'
                                })
                            },
                            complete: function () {
                                layer.closeAll("loading");
                            }
                        });
                        return false;
                    });
                    $("select#options-input_type").bind('change', onCheckCanTypeInValue).trigger('change');
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    alert("ajax错误," + textStatus + ' : ' + errorThrown);
                },
                complete: function (XMLHttpRequest, textStatus) {
                }
            });
            return false;
        })
        $("a.btn_edit").click(function () {
            var name = $(this).parents("div.form-group").children("label").html();
            $.ajax({
                url: $(this).attr('href'),
                success: function (data) {
                    layer.open({
                        type: 1,
                        title: '<?=Yii::t('app', 'Update')?> ' + name,
                        maxmin: true,
                        shadeClose: true, //点击遮罩关闭层
                        area: ['70%', '80%'],
                        content: data,
                    });
                    $("form[name=custom]").bind('submit', function () {
                        var $form = $(this);
                        $.ajax({
                            url: $form.attr('action'),
                            type: "post",
                            data: $form.serialize(),
                            beforeSend: function () {
                                layer.load(2,{
                                    shade: [0.1,'#fff'] //0.1透明度的白色背景
                                });
                            },
                            success: function (data) {
                                location.href = "<?=Url::toRoute(['custom'])?>";
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                layer.alert(jqXHR.responseJSON.message, {
                                    title:tips.error,
                                    btn: [tips.ok],
                                    icon: 2,
                                    skin: 'layer-ext-moon'
                                })
                            },
                            complete: function () {
                                layer.closeAll("loading");
                            }
                        });
                        return false;
                    });
                    $("select#options-input_type").bind('change', onCheckCanTypeInValue).trigger('change');
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

    function onCheckCanTypeInValue() {
        var type = $(this).val();
        var restrictTypeTips = '<?=Yii::t('app', 'Type restrict, please type in after create')?>';
        var input = $("form[name=custom] input#options-value");
        if(type != <?=Constants::INPUT_INPUT?> && type != <?=Constants::INPUT_TEXTAREA?>){
            if( input.val() == restrictTypeTips ){
                input.val(input.attr('oldValue'));
            }else{
                input.attr('oldValue', input.val());
            }
            input.val(restrictTypeTips).attr('disabled', true);
        }else{
            if( input.val() == '<?=Yii::t('app', 'Type restrict, please type in after create')?>' ){
                input.val(input.attr('oldValue'));
            }else{
                input.attr('oldValue', input.val());
            }
            input.attr('disabled', false);
        }
    }
</script>
<?php JsBlock::end() ?>
