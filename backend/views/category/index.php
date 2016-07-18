<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/21
 * Time: 14:14
 */
use feehi\grid\GridView;
use feehi\widgets\Bar;

$this->title = "Category";
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
                            'attribute' => 'name',
                            'label' => yii::t('app', 'Name'),
                            'format' => 'html',
                            'value' => function($model,$key,$index,$column){
                                $return = '';
                                for($i=0; $i<$model['level']; $i++){
                                    $return .= "&nbsp;&nbsp;&nbsp;&nbsp;";
                                }
                                return $return.$model['name'];
                            }
                        ],
                        [
                            'attribute' => 'sort',
                            'label' => yii::t('app', 'Sort'),
                            'format' => 'raw',
                            'value' => function($model){
                                return \yii\helpers\Html::input('number', "sort[{$model['id']}]", $model['sort'], ['style'=>'width:50px']);
                            }
                        ],

                        [
                            'label' => yii::t('app', 'Created At'),
                            'attribute' => 'created_at',
                            'format' => 'date',
                        ],
                        [
                            'label' => yii::t('app', 'Updated At'),
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
