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
 */

use backend\widgets\ActiveForm;
use common\widgets\JsBlock;
use yii\helpers\Url;

$this->title = Yii::t('app', 'SMTP Setting');
$this->params['breadcrumbs'][] = Yii::t('app', 'SMTP Setting');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <?=$this->render('/widgets/_ibox-title');?>
            <div class="ibox-content">
                <?php $form = ActiveForm::begin(); ?>
                <?php
                 $template = "{label}\n<div class='col-sm-8'>{input}\n{error}</div>\n{hint}<div class='col-sm-2'><span class='tips'> {{%TIPS%}}</span></div>";
                ?>
                <?= $form->field($model, 'smtp_host', ['template' => str_replace("{{%TIPS%}}", "<i class='fa fa-info-circle'></i> " . yii::t('app', 'smtp.xxx.com'), $template)]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'smtp_port', ['template' => str_replace("{{%TIPS%}}", "<i class='fa fa-info-circle'></i> " . yii::t('app', '25/465/587'), $template)]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'smtp_username', ['template' => str_replace("{{%TIPS%}}", "<i class='fa fa-info-circle'></i> " . "x@xx.com", $template)]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'smtp_password', ['template' => str_replace("{{%TIPS%}}", "", $template)])->textInput() ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'smtp_nickname', ['template' => str_replace("{{%TIPS%}}", "<i class='fa fa-info-circle'></i> " . "xx", $template)])->textInput() ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'smtp_encryption', ['template' => str_replace("{{%TIPS%}}", "<i class='fa fa-info-circle'></i> " . "tls/ssl", $template)]) ?>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <div class="col-sm-4 col-sm-offset-2">
                        <button class="btn btn-primary" type="submit"><?= Yii::t('app', 'Save') ?></button>
                        <button id="test" class="btn btn-success" type="button"><?= Yii::t('app', 'Test') ?></button>
                        <button class="btn btn-white" type="reset"><?= Yii::t('app', 'Reset') ?></button>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php JsBlock::begin(); ?>
<script>
    $(document).ready(function () {
        $("#test").click(function () {
            layer.msg('<?=Yii::t('app', 'Loading, hold on please...')?>', {icon: 16, 'time': 0});
            $.ajax({
                url: '<?=Url::to(['test-smtp'])?>',
                method: 'post',
                data: $("form").serialize(),
                success: function (data) {
                    layer.msg("<?=Yii::t('app', 'Success')?>");
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    layer.msg(jqXHR.responseJSON.message);
                },
                compelete: function () {
                    layer.closeAll('loading');
                }
            });
            return false;
        });
    })
</script>
<?php JsBlock::end(); ?>
