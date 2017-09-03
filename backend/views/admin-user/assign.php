<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-11 22:34
 */
/**
 * @var $model backend\models\User;
 */
use backend\models\AdminRoles;
use backend\widgets\ActiveForm;
use yii\helpers\Url;

$this->params['breadcrumbs'] = [
    ['label' => yii::t('app', 'Admin Users'), 'url' => Url::to(['index'])],
    ['label' => yii::t('app', 'Assign Roles')],
];
?>
<div class="col-sm-12">
    <div class="ibox">
        <?= $this->render('/widgets/_ibox-title') ?>
        <div class="ibox-content">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'role_id', ['labelOptions' => ['style' => 'display:none']])->radioList(AdminRoles::getRolesNames()) ?>
            <div class="hr-line-dashed"></div>
            <?= $form->defaultButtons() ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
