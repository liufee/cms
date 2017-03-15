<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-23 17:51
 */

use backend\grid\GridView;
use yii\helpers\Url;
use common\libs\Constants;
use yii\helpers\Html;
use backend\widgets\Bar;
use backend\models\Article;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;

$this->title = 'Pages';

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
                    'layout' => "{items}\n{pager}",
                    'columns' => [
                        [
                            'class' => CheckboxColumn::class,
                        ],
                        [
                            'attribute' => 'id',
                        ],
                        [
                            'attribute' => 'sort',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return Html::input('number', "sort[{$model['id']}]", $model['sort'], ['style' => 'width:50px']);
                            }
                        ],
                        [
                            'attribute' => 'title',
                            'format' => 'html',
                            'width' => '170',
                            'value' => function ($model, $key, $index, $column) {
                                return Html::a($model->title, 'javascript:void(0)', [
                                    'title' => $model->thumb,
                                    'class' => 'title'
                                ]);
                            }
                        ],
                        [
                            'attribute' => 'author_name',
                        ],
                        [
                            'attribute' => 'status',
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $column) {
                                if ($model->status == Article::ARTICLE_PUBLISHED) {
                                    $url = Url::to([
                                        'change-status',
                                        'id' => $model->id,
                                        'status' => 0,
                                        'field' => 'status'
                                    ]);
                                    $class = 'btn btn-info btn-xs btn-rounded';
                                    $confirm = Yii::t('app', 'Are you sure you want to cancel release?');
                                } else {
                                    $url = Url::to([
                                        'change-status',
                                        'id' => $model->id,
                                        'status' => 1,
                                        'field' => 'status'
                                    ]);
                                    $class = 'btn btn-default btn-xs btn-rounded';
                                    $confirm = Yii::t('app', 'Are you sure you want to publish?');
                                }
                                return Html::a(Constants::getArticleStatus($model->status), $url, [
                                    'class' => $class,
                                    'data-confirm' => $confirm,
                                    'data-method' => 'post',
                                    'data-pjax' => '0',
                                ]);

                            },
                            'filter' => Constants::getArticleStatus(),
                        ],
                        [
                            'attribute' => 'created_at',
                            'format' => ['date'],
                            'filter' => Html::activeInput('text', $searchModel, 'create_start_at', [
                                    'class' => 'form-control layer-date',
                                    'placeholder' => '',
                                    'onclick' => "laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'});"
                                ]) . \yii\helpers\Html::activeInput('text', $searchModel, 'create_end_at', [
                                    'class' => 'form-control layer-date',
                                    'placeholder' => '',
                                    'onclick' => "laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})"
                                ]),
                        ],
                        [
                            'attribute' => 'updated_at',
                            'format' => ['date'],
                            'filter' => Html::activeInput('text', $searchModel, 'update_start_at', [
                                    'class' => 'form-control layer-date',
                                    'placeholder' => '',
                                    'onclick' => "laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})"
                                ]) . \yii\helpers\Html::activeInput('text', $searchModel, 'update_end_at', [
                                    'class' => 'form-control layer-date',
                                    'placeholder' => '',
                                    'onclick' => "laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})"
                                ]),
                        ],
                        [
                            'class' => ActionColumn::class,
                            'buttons' => [
                                'comment' => function ($url, $model, $key) {
                                    return Html::a('<i class="fa  fa-commenting-o" aria-hidden="true"></i> ' . Yii::t('app', 'Comments'), Url::to([
                                        'comment/index',
                                        'CommentSearch[aid]' => $model->id
                                    ]), [
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
                ]); ?>
            </div>
        </div>
    </div>
</div>