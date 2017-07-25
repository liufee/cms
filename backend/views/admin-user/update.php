<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-31 17:07
 */

/**
 * @var $model backend\models\User
 * @var $rolesModel backend\models\AdminRoleUser
 */

?>
<?php
if (yii::$app->controller->action->id == 'update') {
    echo $this->render('_form', [
        'model' => $model,
        'rolesModel' => $rolesModel,
    ]);
} else {
    echo $this->render('_form-update-self', [
        'model' => $model
    ]);
}
?>
