<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2018-02-24 14:26
 */
use common\models\AdminUser;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\AdminUser */
?>
<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'username',
        'email',
        [
            'attribute' => 'avatar',
            'format' => 'raw',
            'value' => function($model){
                if( empty( $model->avatar ) ) return '-';
                return "<img style='max-width:100px;max-height:100px' src='" . $model->avatar . "'>";
            }
        ],
        [
            'attribute' => 'roles',
            'label' => Yii::t("app", 'Roles'),
            'value' => function($model){
                /** @var \common\models\AdminUser $model */
                return $model->getRolesNameString();
            }
        ],
        [
            'attribute' => 'status',
            'value' => function ($model) {
                if($model->status == AdminUser::STATUS_ACTIVE){
                    return Yii::t('app', 'Normal');
                }else if( $model->status == AdminUser::STATUS_DELETED ) {
                    return Yii::t('app', 'Disabled');
                }
            }
        ],
        'created_at:datetime',
        'updated_at:datetime',
    ],
]) ?>