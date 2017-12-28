<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-09-12 11:50
 */

/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ArrayDataProvider
 * @var $searchModel backend\models\form\Rbac
 */

use backend\grid\GridView;
use backend\grid\SortColumn;
use backend\widgets\Bar;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;

$this->title = "Roles";
$this->params['breadcrumbs'][] = yii::t('app', 'Roles');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget([
                    'buttons' => [
                        'create' => function () {
                            return Html::a('<i class="fa fa-plus"></i> ' . yii::t('app', 'Create'), Url::to(['role-create']), [
                                'title' => yii::t('app', 'Create'),
                                'data-pjax' => '0',
                                'class' => 'btn btn-white btn-sm',
                            ]);
                        },
                        'delete' => function () {
                            return Html::a('<i class="fa fa-trash-o"></i> ' . yii::t('app', 'Delete'), Url::to(['role-delete']), [
                                'title' => yii::t('app', 'Delete'),
                                'data-pjax' => '0',
                                'data-confirm' => yii::t('app', 'Really to delete?'),
                                'class' => 'btn btn-white btn-sm multi-operate',
                            ]);
                        }
                    ],
                    'template' => '{refresh} {create} {delete}'
                ]) ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        [
                            'class' => CheckboxColumn::className(),
                            'checkboxOptions' => function ($model, $key, $index, $column) {
                                return ['value' => $model->name];
                            }
                        ],
                        [
                            'attribute' => 'name',
                        ],
                        [
                            'attribute' => 'description',
                        ],
                        [
                            'class' => SortColumn::className(),
                            'primaryKey' => function($model){
                                return $model['name'];
                            },
                            'action' => Url::to(['roles-sort']),
                        ],
                        [
                            'class' => ActionColumn::className(),
                            'width' => '190px',
                            'buttons' => [
                                'update' => function ($url, $model, $key) {
                                    return Html::a('<i class="fa  fa-edit" aria-hidden="true"></i> ' . Yii::t('app', 'Update'), Url::to([
                                        'role-update',
                                        'name' => $model['name']
                                    ]), [
                                        'title' => Yii::t('app', 'Update'),
                                        'data-pjax' => '0',
                                        'class' => 'btn btn-white btn-sm J_menuItem',
                                    ]);
                                },
                                'delete' => function ($url, $model) {
                                    return Html::a('<i class="fa fa-trash-o"></i> ' . yii::t('app', 'Delete'), Url::to(['role-delete', 'name'=>$model['name']]), [
                                        'title' => yii::t('app', 'Delete'),
                                        'data-pjax' => '0',
                                        'data-confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                        'class' => 'btn btn-white btn-sm',
                                    ]);
                                },
                            ],
                            'template' => '{update} {delete}',
                        ]
                    ]
                ]) ?>
            </div>
        </div>
    </div>
</div>