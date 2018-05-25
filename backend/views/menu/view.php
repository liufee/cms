<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2018-02-24 22:03
 */

use common\libs\Constants;
use yii\widgets\DetailView;

/** @var $model backend\models\Menu */
?>
<?=DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'parent_id',
        [
            'label' => Yii::t('app', 'Parent Menu Name'),
            'attribute' => 'parent_id',
            'value' => function($model){
                return $model->parent === null ? '' : $model->parent->name;
            }
        ],
        'name',
        'url',
        [
            'attribute' => 'icon',
            'format' => 'raw',
            'value' => function($model){
                if( empty($model->icon) ) return '';
                return "<i class='" . $model->icon . "'></i>";
            }
        ],
        'sort',
        [
            'attribute' => 'is_absolute_url',
            'value' => function($model){
                return Constants::getYesNoItems($model->is_absolute_url);
            }
        ],
        [
            'attribute' => 'target',
            'value' => function($model){
                return Constants::getTargetOpenMethod($model->target);
            }
        ],
        [
            'attribute' => 'is_display',
            'value' => function($model){
                return Constants::getYesNoItems($model->is_display);
            }
        ],
        'created_at:datetime',
        'updated_at:datetime',
    ],
])
?>