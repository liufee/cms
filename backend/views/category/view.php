<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2018-02-24 22:14
 */
use yii\widgets\DetailView;

/** @var $model common\models\Category */
?>
<?=DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'parent_id',
        [
            'label' => yii::t('app', 'Parent Category Name'),
            'attribute' => 'parent_id',
            'value' => function($model){
                return $model->parent === null ? '' : $model->parent->name;
            }
        ],
        'name',
        'alias',
        'sort',
        'remark',
        'created_at:datetime',
        'updated_at:datetime',
    ],
])?>
