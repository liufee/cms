<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-09-12 22:02
 */
use yii\helpers\Url;

$this->params['breadcrumbs'] = [
    ['label' => yii::t('app', 'Roles'), 'url' => Url::to(['roles'])],
    ['label' => yii::t('app', 'Create') . yii::t('app', 'Roles')],
];

?>
<?= $this->render('_role-form', [
    'model' => $model,
]) ?>