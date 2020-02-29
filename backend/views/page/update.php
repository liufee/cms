<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-24 12:51
 */

use yii\helpers\Url;

$this->params['breadcrumbs'] = [
    ['label' => Yii::t('app', 'Pages'), 'url' => Url::to(['index'])],
    ['label' => Yii::t('app', 'Update') . Yii::t('app', 'Pages')],
];
/**
 * @var $model common\models\Article
 * @var $contentModel common\models\Article
 */
?>
<?= $this->render('_form', [
    'model' => $model,
    'contentModel' => $contentModel
]);
