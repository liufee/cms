<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/21
 * Time: 14:14
 */
use feehi\grid\GridView;
use feehi\widgets\Bar;

$this->title = "Friendly Links";
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
                            'attribute' => 'name'
                        ],
                        [
                            'attribute' => 'url',
                            'format' => 'html',
                            'value' => function($model){
                                return \yii\bootstrap\Html::a($model->url, $model->url);
                            }
                        ],
                        [
                            'attribute' => yii::t('app', 'Sort'),
                            'format' => 'raw',
                            'value' => function($model){
                                return \yii\helpers\Html::input('number', "sort[{$model['id']}]", $model['sort']);
                            }
                        ],
                        [
                            'label' => yii::t('app', 'Status'),
                            'attribute' => 'status',
                            'format' => 'html',
                            'value' => function($model){
                                if($model['status']){
                                    return "<a class=\"btn btn-info btn-xs btn-rounded\" href=\"javascript:void(0)\">显示</a>";
                                }else{
                                    return "<a class=\"btn btn-default btn-xs btn-rounded\" href=\"javacript:void(0)\">隐藏</a>";
                                }
                            }
                        ],
                        [
                            'attribute' => 'created_at',
                            'format' => 'date',
                        ],
                        [
                            'attribute' => 'updated_at',
                            'format' => 'date',
                        ],
                        [
                            'class' => 'feehi\grid\ActionColumn',
                        ]
                    ]
                ])
                ?>
            </div>
        </div>
    </div>
</div>