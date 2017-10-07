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

use backend\widgets\ActiveForm;
use backend\models\User;
use common\widgets\JsBlock;
use backend\models\form\Rbac;
use yii\helpers\ArrayHelper;

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
                <?= $form->field($model, 'avatar')->imgInput([
                    'width' => '200px',
                    'baseUrl' => yii::$app->params['admin']['url']
                ]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'email')->textInput(['maxlength' => 64]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'password')->passwordInput(['maxlength' => 512]) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'status')->radioList( User::getStatuses() ) ?>
                <div class="hr-line-dashed"></div>
                <?php
                    $roles = yii::$app->getAuthManager()->getRoles();
                    $temp = [];
                    foreach (array_keys($roles) as $key){
                        $temp[$key] = $key;
                    }
                ?>
                <?php
                    $itemsOptions = [];
                    if(in_array( $model->getId(), yii::$app->getBehavior('access')->superAdminUserIds)){
                        $itemsOptions = ['disabled'=>'true'];
                    }//var_dump($itemsOptions);exit;
                ?>
                <?= $form->field($model, 'roles', [
                    'labelOptions' => [
                        'label' => yii::t('app', 'Roles'),
                    ]
                ])->checkboxList($temp, ['itemOptions'=>$itemsOptions]) ?>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"> <?=yii::t('app', 'Permissions')?></label>
                    <div class="col-sm-10">
                        <?php
                        $itemsOptions = [];
                        if(in_array($model->getId(), yii::$app->getBehavior('access')->superAdminUserIds)){
                            $itemsOptions = ['disabled'=>'true'];
                        }
                        $rbac = new Rbac();
                        foreach ($rbac->getPermissionsByGroup('form') as $key => $value){
                            echo "<div class='col-sm-1 text-center'><h2>{$key}</h2></div>";
                            echo "<div class='col-sm-11'>";
                            foreach ($value as $k => $val){
                                echo $form->field($model, 'permissions', ['labelOptions'=>['class'=>'col-sm-1']])->label($k)->checkboxList(ArrayHelper::map($val, 'name', 'description'), ['itemOptions'=>$itemsOptions]);
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
        $("form").bind("beforeSubmit", function () {
            var permissions = [];
            $("div.field-user-permissions input[type=checkbox]:checked").each(function(){
                permissions.push($(this).val());

            });
            $(this).append("<input name='User[permissions]' value='" + permissions + "'>")
        })
    </script>
<?php JsBlock::end()?>