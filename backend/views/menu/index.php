<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-21 14:14
 */

use backend\grid\GridView;
use backend\widgets\Bar;
use yii\helpers\Html;
use yii\helpers\Url;
use common\libs\Constants;
use backend\models\Menu;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;

$this->title = "Backend Menus";
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget() ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        [
                            'class' => CheckboxColumn::class,
                        ],
                        [
                            'attribute' => 'name',
                            'label' => yii::t('app', 'Name'),
                            'format' => 'html',
                            'value' => function ($model, $key, $index, $column) {
                                $return = '';
                                for ($i = 0; $i < $model['level']; $i++) {
                                    $return .= "&nbsp;&nbsp;&nbsp;&nbsp;";
                                }
                                $name = yii::t('menu', $model['name']);
                                return $return . $name;
                            }
                        ],
                        [
                            'attribute' => 'icon',
                            'label' => yii::t('app', 'Icon'),
                            'format' => 'html',
                            'value' => function ($model) {
                                return "<i class=\"fa {$model['icon']}\"></i>";
                            }
                        ],
                        [
                            'attribute' => 'url',
                            'label' => yii::t('app', 'Url'),
                        ],
                        [
                            'attribute' => 'sort',
                            'label' => yii::t('app', 'Sort'),
                            'format' => 'raw',
                            'value' => function ($model) {
                                return Html::input('number', "sort[{$model['id']}]", $model['sort']);
                            }
                        ],
                        [
                            'attribute' => 'method',
                            'label' => yii::t('app', 'HTTP Method'),
                            'value' => function ($model) {
                                return Constants::getHttpMethodItems($model['method']);
                            },
                            'filter' => Constants::getHttpMethodItems(),
                        ],
                        [
                            'attribute' => 'is_display',
                            'label' => yii::t('app', 'Is Display'),
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $column) {
                                if ($model['is_display'] == Menu::DISPLAY_YES) {
                                    $url = Url::to([
                                        'change-status',
                                        'id' => $model['id'],
                                        'status' => Menu::DISPLAY_NO,
                                        'field' => 'is_display'
                                    ]);
                                    $class = 'btn btn-info btn-xs btn-rounded';
                                    $confirm = Yii::t('app', 'Are you sure you want to disable this item?');
                                } else {
                                    $url = Url::to([
                                        'change-status',
                                        'id' => $model['id'],
                                        'status' => Menu::DISPLAY_YES,
                                        'field' => 'is_display'
                                    ]);
                                    $class = 'btn btn-default btn-xs btn-rounded';
                                    $confirm = Yii::t('app', 'Are you sure you want to enable this item?');
                                }
                                return Html::a(Constants::getYesNoItems($model['is_display']), $url, [
                                    'class' => $class,
                                    'data-confirm' => $confirm,
                                    'data-method' => 'post',
                                    'data-pjax' => '0',
                                ]);

                            },
                            'filter' => Constants::getYesNoItems(),
                        ],
                        [
                            'attribute' => 'created_at',
                            'label' => yii::t('app', 'Created At'),
                            'format' => 'date'
                        ],
                        [
                            'attribute' => 'updated_at',
                            'label' => yii::t('app', 'Updated At'),
                            'format' => 'date',
                        ],
                        [
                            'class' => ActionColumn::class,
                            'width' => '190px',
                            'buttons' => [
                                'create' => function ($url, $model, $key) {
                                    return Html::a('<i class="fa  fa-plus" aria-hidden="true"></i> ' . Yii::t('app', 'Create'), Url::to([
                                        'create',
                                        'parent_id' => $model['id']
                                    ]), [
                                        'title' => Yii::t('app', 'Create'),
                                        'data-pjax' => '0',
                                        'class' => 'btn btn-white btn-sm J_menuItem',
                                    ]);
                                }
                            ],
                            'template' => '{create} {update} {delete}',
                        ]
                    ]
                ]) ?>
            </div>
        </div>
    </div>
</div>