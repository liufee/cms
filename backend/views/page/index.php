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
use feehi\widgets\Bar;
use backend\models\Article;

$this->title = 'Pages';

?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget()?>
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
                            'attribute' => 'sort',
                            'format' => 'raw',
                            'value' => function($model){
                                return Html::input('number', "sort[{$model['id']}]", $model['sort'], ['style'=>'width:50px']);
                            }
                        ],
                        [
                            'attribute' => 'title',
                            'format' => 'html',
                            'width' => '170',
                            'value' => function($model, $key, $index, $column){
                                return Html::a($model->title, 'javascript:void(0)', ['title'=>$model->thumb, 'class'=>'title']);
                            }
                        ],
                        [
                            'attribute' => 'author_name',
                        ],
                        [
                            'attribute' => 'status',
                            'format' => 'html',
                            'value' => function($model, $key, $index, $column) {
                                $text = Constants::getArticleStatus($model->status);
                                if($model->status == Article::ARTICLE_PUBLISHED){
                                    $url = Url::to(['change-status', 'id'=>$model->id, 'status'=>Article::ARTICLE_DRAFT]);
                                    return "<a href='$url' class='btn btn-info btn-xs btn-rounded'>{$text}</a>";
                                }else{
                                    $url = Url::to(['change-status', 'id'=>$model->id, 'status'=>Article::ARTICLE_PUBLISHED]);
                                    return "<a href='$url' class='btn  btn-xs btn-default btn-rounded'>{$text}</a>";
                                }
                            },
                            'filter' => Constants::getArticleStatus(),
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
                            'buttons' => [
                                'comment' => function($url, $model, $key){
                                    return Html::a('<i class="fa  fa-commenting-o" aria-hidden="true"></i> '. Yii::t('app', 'Comments'), Url::to(['comment/index', 'CommentSearch[aid]'=>$model->id]), [
                                        'title' => Yii::t('app', 'Comments'),
                                        'data-pjax' => '0',
                                        'class' => 'btn btn-white btn-sm J_menuItem',
                                    ]);
                                }
                            ],
                            'width' => '135',
                            'template' => '{view-layer} {update} {delete}{comment}',
                        ],
                    ]
                ]);
                ?>
            </div>
        </div>
    </div>
</div>