<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-09-13 09:51
 */

use yii\helpers\Url;

/**
 * @var $this yii\web\View
 * @var $model backend\models\form\RbacForm
 */

$this->params['breadcrumbs'] = [
    ['label' => Yii::t('app', 'Roles'), 'url' => Url::to(['roles'])],
    ['label' => Yii::t('app', 'Create') . Yii::t('app', 'Roles')],
];

?>
<?= $this->render('_role-form', [
    'model' => $model,
]) ?>