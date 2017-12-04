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
 * @var $banner common\models\Options
 */
$this->params['breadcrumbs'] = [
    ['label' => yii::t('app', 'Banner Types'), 'url' => Url::to(['index'])],
    ['label' => yii::t('app', 'Banner') . ' (' . $banner->tips . "-{$banner->name})", 'url' => Url::to(['banners', 'id'=>$banner->id])],
    ['label' => yii::t('app', 'Create') . yii::t('app', 'Banner')],
];
?>
<?= $this->render('_banner_form', [
    'model' => $model,
]);