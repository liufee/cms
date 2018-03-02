<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2018-02-24 21:04
 */
use common\libs\Constants;
use yii\widgets\DetailView;

/**
 * @var $model backend\models\form\AdForm
 */
?>
<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'name',
        [
            'attribute' => 'input_type',
            'value' => function($model){
                return Constants::getAdTypeItems($model->input_type);
            }
        ],
        'tips',
        [
            'attribute' => 'ad',
            'format' => 'raw',
            'value' => function($model){
                switch ($model->input_type){
                    case Constants::AD_IMG:
                        return "<img style='max-width: 200px;max-height: 150px' src='{$model->ad}'>";
                    case Constants::AD_VIDEO:
                        return "<video style='max-width: 200px;max-height: 150px' src='{$model->ad}' controls='controls'></video>";
                    case Constants::AD_TEXT:
                        return $model->ad;
                }
            }
        ],
        'link',
        'desc',
        [
            'attribute' => 'autoload',
            'value' => function($model){
                return Constants::getYesNoItems($model->autoload);
            }
        ],
        'sort',
        [
            'attribute' => 'target',
            'value' => function($model){
                return Constants::getTargetOpenMethod($model->target);
            }
        ],
        'created_at:datetime',
        'updated_at:datetime'
    ],
]);