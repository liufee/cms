<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

/* @var $this yii\web\View */
/* @var $model common\models\Options */
/* @var $form ActiveForm */

use backend\widgets\ActiveForm;
use common\widgets\JsBlock;
use yii\helpers\Url;

$this->title = yii::t('app', 'SMTP Setting');
?>
<div class="row">
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
                <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'smtp_host') ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'smtp_port') ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'smtp_username') ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'smtp_password')->textInput() ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'smtp_nickname')->textInput() ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'smtp_encryption') ?>
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
                    if (data == '1') {
                        layer.msg("<?=yii::t('app', 'Success')?>");
                    } else {
                        layer.msg(data);
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    layer.msg(XMLHttpRequest.responseText);
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
