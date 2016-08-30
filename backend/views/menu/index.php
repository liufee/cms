<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/21
 * Time: 14:14
 */
use feehi\grid\GridView;
use feehi\widgets\Bar;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = "Backend Menus";
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget()?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        [
                            'class' => 'feehi\grid\CheckboxColumn',
                        ],
                        [
                            'attribute' => 'Name',
                            'label' => yii::t('app', 'Name'),
                            'format' => 'html',
                            'value' => function($model,$key,$index,$column){
                                $return = '';
                                for($i=0; $i<$model['level']; $i++){
                                    $return .= "&nbsp;&nbsp;&nbsp;&nbsp;";
                                }
                                $name = yii::t('menu', $model['name']);
                                return $return.$name;
                            }
                        ],
                        [
                            'attribute' => 'icon',
                            'label' => yii::t('app', 'Icon'),
                            'format' => 'html',
                            'value' => function($model){
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
                            'value' => function($model){
                                return Html::input('number', "sort[{$model['id']}]", $model['sort']);
                            }
                        ],
                        [
                            'attribute' => 'is_display',
                            'label' => yii::t('app', 'Is Display'),
                            'format' => 'html',
                            'value' => function($model){
                                if($model['is_display']){
                                    return "<a class=\"btn btn-info btn-xs btn-rounded\" href=\"javascript:void(0)\">显示</a>";
                                }else{
                                    return "<a class=\"btn btn-default btn-xs btn-rounded\" href=\"javacript:void(0)\">隐藏</a>";
                                }
                            }
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
                            'class' => 'feehi\grid\ActionColumn',
                            'buttons' => [
                                'create' => function($url, $model, $key){
                                    return Html::a('<i class="fa  fa-plus" aria-hidden="true"></i> '. Yii::t('app', 'Create'), Url::to(['create', 'parent_id'=>$model['id']]), [
                                        'title' => Yii::t('app', 'Create'),
                                        'data-pjax' => '0',
                                        'class' => 'btn btn-white btn-sm J_menuItem',
                                    ]);
                                }
                            ],
                            'template' => '{create} {update} {delete}',
                        ]
                    ]
                ])
                ?>
            </div>
        </div>
    </div>
</div>