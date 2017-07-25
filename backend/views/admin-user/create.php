<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-25 11:14
 */

/**
 * @var $model backend\models\User
 * @var $rolesModel backend\models\AdminRoles
 */

?>
<?= $this->render('_form', [
    'model' => $model,
    'rolesModel' => $rolesModel,
]); ?>
