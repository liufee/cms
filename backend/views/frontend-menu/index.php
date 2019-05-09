<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-21 14:14
 */

/**
 * @var $this yii\web\View
 * @var $dataProvider frontend\models\Menu
 */

use backend\grid\DateColumn;
use backend\grid\GridView;
use backend\grid\SortColumn;
use backend\grid\StatusColumn;
use backend\widgets\Bar;
use frontend\models\Menu;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;

$this->title = "Frontend Menus";
$this->params['breadcrumbs'][] = Yii::t('app', 'Frontend Menus');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget() ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => '{items}',
                    'columns' => [
                        [
                            'class' => CheckboxColumn::className(),
                        ],
                        [
                            'attribute' => 'name',
                            'label' => Yii::t('app', 'Name'),
                            'format' => 'html',
                            'value' => function ($model, $key, $index, $column) {
                                $return = '';
                                for ($i = 0; $i < $model['level']; $i++) {
                                    $return .= "&nbsp;&nbsp;&nbsp;&nbsp;";
                                }
                                return $return . $model['name'];
                            }
                        ],
                        [
                            'attribute' => 'icon',
                            'label' => Yii::t('app', 'Icon'),
                            'format' => 'html',
                            'value' => function ($model) {
                                return "<i class=\"fa {$model['icon']}\"></i>";
                            }
                        ],
                        [
                            'attribute' => 'url',
                            'label' => Yii::t('app', 'Url'),
                        ],
                        [
                            'class' => SortColumn::className(),
                            'primaryKey' => function($model){
                                return ["id" => $model["id"]];
                            },
                            'label' => Yii::t('app', 'Sort')
                        ],
                        [
                            'attribute' => 'is_display',
                            'class' => StatusColumn::className(),
                            'label' => Yii::t('app', 'Is Display'),
                            'formName' => (new Menu)->formName() . '[is_display]',
                        ],
                        [
                            'class' => DateColumn::className(),
                            'attribute' => 'created_at',
                            'label' => Yii::t('app', 'Created At'),
                        ],
                        [
                            'class' => DateColumn::className(),
                            'attribute' => 'updated_at',
                            'label' => Yii::t('app', 'Updated At'),
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
                            'width' => '190px'
                        ]
                    ]
                ]) ?>
            </div>
        </div>
    </div>
</div>