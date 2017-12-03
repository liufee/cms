<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-12-03 22:17
 */

use yii\helpers\Url;

$this->params['breadcrumbs'] = [
    ['label' => yii::t('app', 'Banner Types'), 'url' => Url::to(['index'])],
    ['label' => yii::t('app', 'Update') . yii::t('app', 'Banner Types')],
];
/**
 * @var $model backend\models\form\BannerForm
 */
?>
<?= $this->render('_form', [
    'model' => $model,
]);