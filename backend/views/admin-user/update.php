<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-31 17:07
 */
?>
<?php
if (yii::$app->controller->action->id == 'update') {
    echo $this->render('_form', [
        'model' => $model,
        'rolesModel' => $rolesModel,
        'roles' => $roles,
    ]);
} else {
    echo $this->render('_form-update-self', [
        'model' => $model
    ]);
}
?>
