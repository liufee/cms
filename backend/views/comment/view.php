<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2018-02-24 22:33
 */
use common\libs\Constants;
use yii\widgets\DetailView;

/** @var $model backend\models\Comment */
?>
<?=DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'aid',
        [
            'label' => yii::t('app', 'Article Title'),
            'attribute' => 'aid',
            'value' => function($model){
                return $model->article->title;
            }
        ],
        'uid',
        'admin_id',
        'reply_to',
        'nickname',
        'email',
        'website_url',
        'content',
        'ip',
        [
            'attribute' => 'status',
            'value' => function($model){
                return Constants::getCommentStatusItems($model->status);
            }
        ],
        'created_at:datetime',
        'updated_at:datetime',
    ]
])?>
