<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-12-03 23:09
 */

/**
 * @var $model backend\models\form\BannerForm
 * @var $bannerType \backend\models\form\BannerTypeForm
 */

use yii\helpers\Url;

$this->params['breadcrumbs'] = [
    ['label' => Yii::t('app', 'Banner Types'), 'url' => Url::to(['index'])],
    ['label' => Yii::t('app', 'Banner') . ' (' . $bannerType->tips . "-{$bannerType->name})", 'url' => Url::to(['banners', 'id'=>$bannerType->id])],
    ['label' => Yii::t('app', 'Update') . Yii::t('app', 'Banner')],
];

?>
<?= $this->render('_banner_form', [
    'model' => $model,
]);