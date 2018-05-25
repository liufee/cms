<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-21 14:32
 */

use yii\helpers\Url;

$this->params['breadcrumbs'] = [
    ['label' => Yii::t('app', 'Frontend Menus'), 'url' => Url::to(['index'])],
    ['label' => Yii::t('app', 'Update') . Yii::t('app', 'Frontend Menus')],
];
/**
 * @var $model frontend\models\Menu
 */
?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>