<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2018-02-24 20:48
 */
use common\libs\Constants;
use yii\widgets\DetailView;

/**
 * @var $model backend\models\form\BannerForm
 */
?>
<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'name',
        'tips',
        [
            'attribute' => 'img',
            'format' => 'raw',
            'value' => function($model){
                if( empty($model->img) ) return '';
                return "<img style='max-width:200px;max-height:200px' src='" . $model->img . "'>";
            }
        ],
        [
            'attribute' => 'target',
            'value' => function($model){
                return Constants::getTargetOpenMethod($model->target);
            }
        ],
        'link',
        'sort',
        [
            'attribute' => 'status',
            'value' => function($model){
                return Constants::getStatusItems($model->status);
            }
        ],
        'desc',
    ],
]);
