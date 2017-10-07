<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-09-12 14:46
 */

/**
 * @var $this yii\web\View
 * @var $model backend\models\form\Rbac
 */

use backend\widgets\ActiveForm;
use common\widgets\JsBlock;
use yii\helpers\ArrayHelper;

$this->title = "Roles";

?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'name')->textInput() ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'description')->textInput() ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'sort')->textInput() ?>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"> <?=yii::t('app', 'Permissions')?></label>
                    <div class="col-sm-10">
                        <?php
                        foreach ($model->getPermissionsByGroup('form') as $key => $value){
                            echo "<div class='col-sm-1 text-center'><h2>{$key}</h2></div>";
                            echo "<div class='col-sm-11'>";
                            foreach ($value as $k => $val){
                                echo $form->field($model, 'permissions', ['labelOptions'=>['class'=>'col-sm-1']])->label($k)->checkboxList(ArrayHelper::map($val, 'name', 'description'));
                            }
                            echo "</div><div class='col-sm-12' style='height: 20px'></div>";
                        }
                        ?>
                        <div class="help-block m-b-none"></div>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <?php
                $roles = yii::$app->getAuthManager()->getRoles();
                $curChainRoles = [];
                if( $model->name != '' ) {
                    $curChainRoles = array_keys(yii::$app->getAuthManager()->getChildRoles($model->name));
                }
                $temp = [];
                foreach ($roles as $role){
                    if( in_array($role->name, $curChainRoles) ) continue;
                    $temp[$role->name] = $role->name;
                }
                ?>
                <?= $form->field($model, 'roles')->label(yii::t('app', 'Roles'))->checkboxList($temp) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->defaultButtons() ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php JsBlock::begin()?>
<script>
    $("form").bind("beforeSubmit", function () {
        var permissions = [];
        $("div.field-rbac-permissions input[type=checkbox]:checked").each(function(){
            permissions.push($(this).val());

        });
        $(this).append("<input name='Rbac[permissions]' value='" + permissions + "'>")
    })
</script>
<?php JsBlock::end()?>
