<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-09-12 12:32
 */
use yii\helpers\Url;

/**
 * @var $this yii\web\View
 * @var $model backend\models\form\Rbac
 */

$this->params['breadcrumbs'] = [
    ['label' => yii::t('app', 'Permissions'), 'url' => Url::to(['permissions'])],
    ['label' => yii::t('app', 'Update') . yii::t('app', 'Permissions')],
];

?>
<?= $this->render('_permission-form', [
    'model' => $model,
]) ?>