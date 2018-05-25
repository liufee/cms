<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-14 10:09
 */

use yii\widgets\DetailView;

/**
 * @var $this yii\web\View
 * @var $model backend\models\AdminLog
 */

$this->title = "Log Detail";
?>
<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'user_id',
        [
            'label' => Yii::t('app', 'Admin'),
            'attribute' => 'user',
            'value' => function($model){
                return $model->user->username;
            }
        ],
        'route',
        'created_at:datetime',
        [
            'attribute' => 'description',
            'format' => 'raw',
        ]
    ],
]) ?>