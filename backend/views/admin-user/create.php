<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-25 11:14
 */
use yii\helpers\Url;

$this->params['breadcrumbs'] = [
    ['label' => yii::t('app', 'Admin Users'), 'url' => Url::to(['index'])],
    ['label' => yii::t('app', 'Create') . yii::t('app', 'Admin Users')],
];
/**
 * @var $model backend\models\User
 */
?>
<?= $this->render('_form', [
    'model' => $model,
]); ?>
