<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2018-02-24 23:02
 */
use yii\widgets\DetailView;

/** @var $model backend\models\form\Rbac */
?>
<?=DetailView::widget([
    'model' => $model,
    'attributes' => [
        'group',
        'category',
        'route',
        'method',
        'description',
        'sort',
    ],
])?>
