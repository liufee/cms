<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-23 17:51
 */

/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel backend\models\search\UserSearch
 */

use backend\grid\DateColumn;
use backend\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use backend\widgets\Bar;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;
use backend\models\User;

$assignment = function ($url, $model) {
    return Html::a('<i class="fa fa-tablet"></i> ' . yii::t('app', 'Assign Roles'), Url::to([
        'assign',
        'uid' => $model['id']
    ]), [
        'title' => 'assignment',
        'class' => 'btn btn-white btn-sm'
    ]);
};

$this->title = "Admin Users";
$this->params['breadcrumbs'][] = yii::t('app', 'Admin Users');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget([
                    'template' => '{refresh} {create} {delete}'
                ]) ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'layout' => "{items}\n{pager}",
                    'columns' => [
                        [
                            'class' => CheckboxColumn::className(),
                        ],
                        [
                            'attribute' => 'username',
                        ],
                        [
                            'attribute' => 'role',
                            'label' => yii::t('app', 'Role'),
                            'value' => function ($model) {
                                return $model->getRoleName();
                            },
                        ],
                        [
                            'attribute' => 'email',
                        ],
                        [
                            'attribute' => 'status',
                            'label' => yii::t('app', 'Status'),
                            'value' => function ($model) {
                                if($model->status == User::STATUS_ACTIVE){
                                    return yii::t('app', 'Normal');
                                }else if( $model->status == User::STATUS_DELETED ){
                                    return yii::t('app', 'Disabled');
                                }
                            },
                            'filter' => User::getStatuses(),
                        ],
                        [
                            'class' => DateColumn::className(),
                            'attribute' => 'created_at',
                            'filter' => Html::activeInput('text', $searchModel, 'create_start_at', [
                                    'class' => 'form-control layer-date',
                                    'placeholder' => '',
                                    'onclick' => "laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'});"
                                ]) . Html::activeInput('text', $searchModel, 'create_end_at', [
                                    'class' => 'form-control layer-date',
                                    'placeholder' => '',
                                    'onclick' => "laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})"
                                ]),
                        ],
                        [
                            'class' => DateColumn::className(),
                            'attribute' => 'updated_at',
                            'filter' => Html::activeInput('text', $searchModel, 'update_start_at', [
                                    'class' => 'form-control layer-date',
                                    'placeholder' => '',
                                    'onclick' => "laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})"
                                ]) . Html::activeInput('text', $searchModel, 'update_end_at', [
                                    'class' => 'form-control layer-date',
                                    'placeholder' => '',
                                    'onclick' => "laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})"
                                ]),
                        ],
                        [
                            'class' => ActionColumn::className(),
                            'template' => '{update}{delete}',
                            'buttons' => ['assignment' => $assignment],
                        ],
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>