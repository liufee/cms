<?php
/**
 * Ahthor: lf
 * Email: job@feehi.com
 * Blog: http://blog.feehi.com
 * Date: 2016/9/1 16:07
 */
use feehi\grid\GridView;
use feehi\widgets\Bar;
use yii\helpers\Html;
use backend\models\FileUsage;
use feehi\libs\Help;

$this->title = 'Files';

?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title', [
                'buttons' => [
                    [
                        'name' => 'Delete',
                        'url' => ['delete'],
                        'options' => [
                            'class' => 'multi-operate btn btn-primary btn-xs',
                            'data-confirm' => yii::t('app', 'Realy to delete?'),
                        ]
                    ]
                ]
            ]) ?>
            <div class="ibox-content">
                <?= Bar::widget([
                    'buttons' => [
                        [
                            'class' => 'btn btn-white btn-sm refresh',
                            'text' => 'Refresh',
                            'url' => ['refresh'],
                            'iClass' => 'fa fa-refresh',
                        ],
                        [
                            'class' => 'btn btn-white btn-sm multi-operate',
                            'text' => 'Delete',
                            'url' => ['delete'],
                            'iClass' => 'fa fa-trash-o',
                        ],
                    ]
                ]) ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'layout' => "{items}\n{pager}",
                    'columns'=>[
                        [
                            'class' => 'feehi\grid\CheckboxColumn',
                        ],
                        [
                            'attribute' => 'id',
                        ],
                        [
                            'attribute' => 'filename',
                        ],
                        [
                            'attribute' => 'uri',
                            'value' => function($model, $key, $index, $column){
                                return str_replace(yii::$app->params['site']['url'], yii::getAlias('@frontend/web'), $model->uri);
                            }
                        ],
                        [
                            'attribute' => 'filesize',
                            'value' => function($model, $key, $index, $columb){
                                return Help::filesize($model->filesize);
                            }
                        ],
                        [
                            'attribute' => 'mime',
                        ],
                        [
                            'attribute' => 'status',
                            'format' => 'html',
                            'value' => function($model){
                                if($model['status']){
                                    return "<a class=\"btn btn-info btn-xs btn-rounded\" href=\"javascript:void(0)\">".yii::t('app', 'Used')."</a>";
                                }else{
                                    return "<a class=\"btn btn-default btn-xs btn-rounded\" href=\"javacript:void(0)\">".yii::t('app', 'Unused')."</a>";
                                }
                            },
                            'filter' => \feehi\libs\Constants::getYesNoItems()
                        ],
                        [
                            'attribute' => 'uri',
                            'label' => yii::t('app', 'Preview'),
                            'filter' => false,
                            'value' => function ($model, $key, $index, $column) {
                                $return = '';
                                switch($model->mime){
                                    case "image/gif" :
                                    case "image/png" :
                                    case "image/jpeg" :
                                    case "image/jpg" :
                                        $return = "<a href='{$model->uri}' target='_blank'><img style='max-width: 100px;max-height: 100px' src='{$model->uri}'>";
                                    break;
                                }
                                return $return;
                            },
                            'format' => 'raw',
                        ],
                        [
                            'label' => yii::t('app', 'Count'),
                            'value' => function($model, $key, $index, $column){
                                return FileUsage::getFileUseCountByFid($model->id);
                            }
                        ],
                        [
                            'attribute' => 'created_at',
                            'format' => ['date'],
                            'filter' => Html::activeInput('text', $searchModel, 'create_start_at', ['class'=>'form-control layer-date', 'placeholder'=>'', 'onclick'=>"laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'});"]).\yii\helpers\Html::activeInput('text', $searchModel, 'create_end_at', ['class'=>'form-control layer-date', 'placeholder'=>'', 'onclick'=>"laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})"]),
                        ],
                        [
                            'attribute' => 'updated_at',
                            'format' => ['date'],
                            'filter' => Html::activeInput('text', $searchModel, 'update_start_at', ['class'=>'form-control layer-date', 'placeholder'=>'', 'onclick'=>"laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})"]).\yii\helpers\Html::activeInput('text', $searchModel, 'update_end_at', ['class'=>'form-control layer-date', 'placeholder'=>'', 'onclick'=>"laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})"]),
                        ],
                        [
                            'class' => 'feehi\grid\ActionColumn',
                            'width' => '135',
                            'template' => '{view-layer} {delete}{comment}',
                        ],
                    ]
                ]);
                ?>
            </div>
        </div>
    </div>
</div>