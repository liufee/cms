<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-09-12 22:02
 */
use yii\helpers\Url;

/**
 * @var $this yii\web\View
 * @var $model backend\models\form\RbacForm
 */

$this->params['breadcrumbs'] = [
    ['label' => Yii::t('app', 'Permissions'), 'url' => Url::to(['permissions'])],
    ['label' => Yii::t('app', 'Create') . Yii::t('app', 'Permissions')],
];

?>
<?= $this->render('_permission-form', [
    'model' => $model,
]) ?>