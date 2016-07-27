<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/23
 * Time: 17:51
 */
use feehi\grid\GridView;
use yii\helpers\Url;
use feehi\libs\Constants;
use yii\helpers\Html;
use feehi\widgets\Button;
use backend\models\Article;
use feehi\widgets\Bar;
use backend\models\Comment;

$this->title = 'Comments';

$viewLayer = function($url, $model){
    return Html::a('<i class="fa fa-pencil"></i> 查看', Url::to(['view','uid'=>$model['id']]), [
        'title' => 'view',
        'class' => 'btn btn-white btn-sm'
    ]);
};
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
                            'attribute' => 'article_title',
                            'label' => yii::t('app', 'Article Title'),
                            'value' => function($model){
                                return Article::getArticleById($model->aid)['title'];
                            }
                        ],
                        [
                            'attribute' => 'nickname',
                        ],
                        [
                            'attribute' => 'content',
                        ],
                        [
                            'attribute' => 'status',
                            'format' => 'html',
                            'value' => function($model, $key, $index, $column) {
                                $text = Constants::getCommentStatusItems($model->status);
                                if($model->status == Comment::STATUS_INIT){
                                    $class = 'btn-default';
                                }else if($model->status == Comment::STATUS_PASSED){
                                    $class = 'btn-info';
                                }else{
                                    $class = 'btn-danger';
                                }
                                return "<a class='btn {$class} btn-xs btn-rounded'>{$text}</a>";
                            },
                            'filter' => Constants::getCommentStatusItems(),
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
                            'buttons' => [
                                'change-status' => function($url, $model, $key){//echo $model->status;die;
                                    if($model->status == Comment::STATUS_INIT || $model->status == Comment::STATUS_UNPASS){
                                        $url .= '&status=1';
                                        $title = Yii::t('app', 'Pass');
                                    }else if($model->status == Comment::STATUS_PASSED){
                                        $url .= "&status=2";
                                        $title = Yii::t('app', 'Unpass');
                                    }
                                    return Html::a('<i class="fa fa-folder"></i> '. $title, $url, [
                                        'title' => $title,
                                        'data-pjax' => '0',
                                        'class' => 'btn btn-white btn-sm',
                                    ]);
                                },
                            ],
                            'template' => '{change-status}{delete}',
                            //'template' => '{view-layer} {update} {delete}',
                        ],
                    ]
                ]);
                ?>
            </div>
        </div>
    </div>
</div>