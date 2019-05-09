<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-21 14:14
 */

/**
 * @var $dataProvider yii\data\ArrayDataProvider
 * @var $model common\models\Category
 */

use backend\grid\DateColumn;
use backend\grid\GridView;
use backend\grid\SortColumn;
use backend\widgets\Bar;
use yii\helpers\Url;
use yii\helpers\Html;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;

$this->title = "Category";
$this->params['breadcrumbs'][] = Yii::t('app', 'Category');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget() ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        [
                            'class' => CheckboxColumn::className(),
                        ],
                        [
                            'attribute' => 'id',
                        ],
                        [
                            'attribute' => 'name',
                            'label' => Yii::t('app', 'Name'),
                            'format' => 'html',
                            'value' => function ($model, $key, $index, $column) {
                                return str_repeat('--', $model['level']) . $model['name'];
                            }
                        ],
                        [
                            'attribute' => 'alias',
                            'label' => Yii::t('app', 'Alias'),
                        ],
                        [
                            'class' => SortColumn::className(),
                            'primaryKey' => function($model){
                                return ["id" => $model["id"]];
                            },
                            'label' => Yii::t('app', 'Sort')
                        ],
                        [
                            'class' => DateColumn::className(),
                            'label' => Yii::t('app', 'Created At'),
                            'attribute' => 'created_at',
                        ],
                        [
                            'class' => DateColumn::className(),
                            'label' => Yii::t('app', 'Updated At'),
                            'attribute' => 'updated_at',
                        ],
                        [
                            'class' => ActionColumn::className(),
                            'buttons' => [
                                'create' => function ($url, $model, $key) {
                                    return Html::a('<i class="fa  fa-plus" aria-hidden="true"></i> ', Url::to([
                                        'create',
                                        'parent_id' => $model['id']
                                    ]), [
                                        'title' => Yii::t('app', 'Create'),
                                        'data-pjax' => '0',
                                        'class' => 'btn-sm J_menuItem',
                                    ]);
                                }
                            ],
                            'template' => '{create} {view-layer} {update} {delete}',
                        ]
                    ]
                ]) ?>
            </div>
        </div>
    </div>
</div>
