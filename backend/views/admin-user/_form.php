<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-25 11:15
 */

/**
 * @var $this yii\web\View
 * @var $model backend\models\User
 */

use backend\models\form\RbacForm;
use backend\widgets\ActiveForm;
use backend\models\User;
use common\widgets\JsBlock;
use yii\helpers\Html;

$this->title = "Admin";
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?php $form = ActiveForm::begin([
                    'options' => [
                        'enctype' => 'multipart/form-data',
                        'class' => 'form-horizontal'
                    ]
                ]); ?>
                <?= $form->field($model, 'username')->textInput(['maxlength' => 64]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'avatar')->imgInput() ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'email')->textInput(['maxlength' => 64]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'password')->passwordInput(['maxlength' => 512]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'status')->radioList( User::getStatuses() ) ?>
                <div class="hr-line-dashed"></div>
                <?php
                    $roles = Yii::$app->getAuthManager()->getRoles();
                    $temp = [];
                    foreach (array_keys($roles) as $key){
                        $temp[$key] = $key;
                    }
                ?>
                <?php
                    $itemsOptions = [];
                    if(in_array( $model->getId(), Yii::$app->getBehavior('access')->superAdminUserIds)){
                        $itemsOptions = ['disabled'=>'true'];
                    }
                ?>
                <?= $form->field($model, 'roles', [
                    'labelOptions' => [
                        'label' => Yii::t('app', 'Roles'),
                    ]
                ])->checkboxList($temp, ['itemOptions'=>$itemsOptions]) ?>
                <div class="hr-line-dashed"></div>
                <div class="form-group field-permissions">
                    <span class="col-sm-2 control-label checkbox checkbox-success"><?= Html::checkbox("", false, ['id'=>'permission-all','class'=>'chooseAll'])?><label for='permission-all'><h4><?=Yii::t('app', 'Permissions')?></h4></label></span>
                    <div class="col-sm-10">
                        <?php
                        $rbac = new RbacForm();
                        foreach ($rbac->getPermissionsByGroup('form') as $key => $value){
                            echo "<div class='col-sm-1 text-left'><span class='checkbox checkbox-success checkbox-inline'>" . Html::checkbox("", false, ['id'=>"permission-all-{$key}", 'class'=>'chooseAll']) . "<label for='permission-all-{$key}'><h4>{$key}</h4></label></span></div>";
                            echo "<div class='col-sm-11'>";
                            foreach ($value as $k => $val){
                                echo "<div class='col-sm-1 text-left'><span class='checkbox checkbox-success checkbox-inline'>" . Html::checkbox("", false, ['id'=>"permission-all-{$k}", 'class'=>'chooseAll']) . "<label for='permission-all-{$k}'><h5>{$k}</h5></label></span></div>";
                                echo "<div class='col-sm-11'>";
                                foreach ($val as $v) {
                                    echo $form->field($model, "permissions[{$v['name']}]", ['options'=>['style'=>'display:inline'], 'labelOptions'=>['class'=>'col-sm-12 control-label']])->checkbox(['value'=>$v['name']])->label($v['description']);
                                }
                                echo "</div><div class='col-sm-12' style='height: 20px'></div>";
                            }
                            echo "</div><div class='col-sm-12' style='height: 20px'></div>";
                        }
                        ?>
                        <div class="help-block m-b-none"></div>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <?= $form->defaultButtons() ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<?php JsBlock::begin()?>
    <script>
        $(document).ready(function () {
            var isSuperAdmin = <?php if(in_array($model->getId(), Yii::$app->getBehavior('access')->superAdminUserIds)){echo 1;}else{echo 0;}?>;
            if( isSuperAdmin ){
                var forbiddens = $(".field-permissions, .field-user-roles").find("input[type=checkbox]");
                forbiddens.each(function(){
                    $(this).attr('disabled', true).attr('checked', true);
                })
            }else{
                var chooseAll = $(".col-sm-11 .col-sm-1 .chooseAll");
                var middle = $(".col-sm-1 .chooseAll");
                var top = $("label .chooseAll");
                for (var i = 0; i < middle.length; i++) {
                    chooseAll.push(middle[i]);
                }
                for (var i = 0; i < top.length; i++) {
                    chooseAll.push(top[i]);
                }
                chooseAll.each(function () {
                    var that = $(this);
                    if (that.attr('id') == 'permission-all') {
                        var checkboxs = $(this).parents("span").next().find("input[type=checkbox]");
                    } else {
                        var checkboxs = $(this).parents(".col-sm-1").next().find("input[type=checkbox]");
                    }
                    var atLeastOneUnchecked = false;
                    checkboxs.each(function () {
                        if ($(this).is(":checked") == false) {
                            atLeastOneUnchecked = true;
                        }
                    })
                    if (atLeastOneUnchecked == false && that.is(":checked") == false) {
                        that.trigger('click');
                    }
                });
            }

            $(".chooseAll").change(function () {
                var type = $(this).is(':checked');
                var checkboxs = $(this).parents("span").next().find("input[type=checkbox]");
                if( checkboxs.length == 0 ) {
                    checkboxs = $(this).parents(".col-sm-1").next().find("input[type=checkbox]");
                }
                checkboxs.each(function () {
                    if(type != $(this).is(':checked')){
                        $(this).trigger('click');
                    }
                })
            })
        })
    </script>
<?php JsBlock::end()?>