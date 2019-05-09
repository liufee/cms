<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-12-03 23:03
 */
/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ArrayDataProvider
 * @var $searchModel backend\models\search\MenuSearch
 * @var $model backend\models\form\BannerForm
 * @var $bannerType common\models\Options
 */

use backend\grid\GridView;
use backend\grid\SortColumn;
use backend\grid\StatusColumn;
use backend\widgets\Bar;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;

$this->title = "Banners";
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Banner Types'), 'url' => Url::to(['index'])];
$this->params['breadcrumbs'][] =  Yii::t('app', 'Banner') . ' (' . $bannerType->tips . "-{$bannerType->name})";
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget([
                    'buttons' => [
                        'create' => function () {
                            return Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Create'), Url::to(['banner-create', 'id' => Yii::$app->getRequest()->get('id')]), [
                                'title' => Yii::t('app', 'Create'),
                                'data-pjax' => '0',
                                'class' => 'btn btn-white btn-sm',
                            ]);
                        },
                        'update' => function () {
                            return Html::a('<i class="fa  fa-sort-numeric-desc"></i> ' . Yii::t('app', 'Sort'), Url::to(['banner-sort',  'id' => Yii::$app->getRequest()->get('id')]), [
                                'title' => Yii::t('app', 'Sort'),
                                'data-pjax' => '0',
                                'param-sign'=>'sign',
                                'class' => 'btn btn-white btn-sm sort',
                            ]);
                        },
                        'delete' => function () {
                            return Html::a('<i class="fa fa-trash-o"></i> ' . Yii::t('app', 'Delete'), Url::to(['banner-delete', 'id' => Yii::$app->getRequest()->get('id')]), [
                                'title' => Yii::t('app', 'Delete'),
                                'data-pjax' => '0',
                                'data-confirm' => Yii::t('app', 'Really to delete?'),
                                'param-sign'=>'sign',
                                'class' => 'btn btn-white btn-sm multi-operate',
                            ]);
                        },
                    ]
                ])?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => '{items}',
                    'columns' => [
                        [
                            'class' => CheckboxColumn::className(),
                            'checkboxOptions' => function($model){
                                return ['value' => $model->sign];
                            }
                        ],
                        [
                            'attribute' => 'img',
                            'label' => Yii::t('app', 'Image'),
                            'format' => 'raw',
                            'value' => function($model){
                                return "<img class='img-responsive' src='{$model->img}'>";
                            }
                        ],
                        [
                            'attribute' => 'link',
                            'label' => Yii::t('app', 'Jump Link'),
                        ],
                        [
                            'attribute' => 'desc',
                            'label' => Yii::t('app', 'Description'),
                        ],
                        [
                            'class' => SortColumn::className(),
                            'label' => Yii::t('app', 'Sort'),
                            'primaryKey' => function($model){
                                return ["sign" => $model->sign];
                            },
                            'action' => Url::to(['banner-sort', 'id'=>Yii::$app->getRequest()->get('id')]),
                        ],
                        [
                            'class' => StatusColumn::className(),
                            'label' => Yii::t('app', 'Status'),
                            'url' => function($model){
                                return Url::to(['banner-update', 'id' => $model['id'], 'sign'=>$model['sign']]);
                            }
                        ],
                        [
                            'class' => ActionColumn::className(),
                            'width' => '190px',
                            'buttons' => [
                                'view-layer' => function ($url, $model, $key, $index, $gridView) {
                                    return Html::a('<i class="fa fa-folder"></i> ', 'javascript:void(0)', [
                                        'title' => Yii::t('yii', 'View'),
                                        'onclick' => "viewLayer('" . Url::toRoute(['banner-view-layer', 'id'=>$model->id, 'sign'=>$model->sign]) . "',$(this))",
                                        'data-pjax' => '0',
                                        'class' => 'btn-sm',
                                    ]);
                                },
                                'update' => function ($url, $model, $key, $index, $gridView) {
                                    return Html::a('<i class="fa fa-edit"></i> ', Url::toRoute(['banner-update', 'id'=>$model->id, 'sign'=>$model->sign]), [
                                        'title' => Yii::t('app', 'Update'),
                                        'data-pjax' => '0',
                                        'class' => 'btn-sm',
                                    ]);
                                },
                                'delete' => function ($url, $model, $key, $index, $gridView) {
                                    return Html::a('<i class="glyphicon glyphicon-trash" aria-hidden="true"></i> ', Url::toRoute(['banner-delete', 'id'=>$model->id, 'sign'=>$model->sign]), [
                                        'title' => Yii::t('app', 'Delete'),
                                        'data-confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                        'class' => 'btn-sm',
                                    ]);
                                }
                            ],
                        ]
                    ]
                ]) ?>
            </div>
        </div>
    </div>
</div>