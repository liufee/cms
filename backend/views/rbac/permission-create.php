<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-09-12 22:02
 */
use yii\helpers\Url;

$this->params['breadcrumbs'] = [
    ['label' => yii::t('app', 'Permissions'), 'url' => Url::to(['permissions'])],
    ['label' => yii::t('app', 'Create') . yii::t('app', 'Permissions')],
];

?>
<?= $this->render('_permission-form', [
    'model' => $model,
]) ?>