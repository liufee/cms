<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-12-03 23:09
 */

use yii\helpers\Url;

/**
 * @var $model backend\models\form\BannerForm
 */
$this->params['breadcrumbs'] = [
    ['label' => yii::t('app', 'Banner Types'), 'url' => Url::to(['index'])],
    ['label' => yii::t('app', 'Banner') . ' (' . $model->bannerType->tips . "-{$model->bannerType->name})", 'url' => Url::to(['banners', 'id'=>$model->bannerType->id])],
    ['label' => yii::t('app', 'Create') . yii::t('app', 'Banner')],
];
?>
<?= $this->render('_banner_form', [
    'model' => $model,
]);