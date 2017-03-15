<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-23 17:51
 */

use backend\grid\GridView;
use common\libs\Constants;
use yii\helpers\Html;
use backend\widgets\Button;
use backend\models\Article;
use backend\widgets\Bar;
use backend\models\Comment;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;

$this->title = 'Comments';

?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget([
                    'template' => "{refresh} {delete}",
                ]) ?>
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
                            'attribute' => 'article_title',
                            'label' => yii::t('app', 'Article Title'),
                            'value' => function ($model) {
                                return Article::getArticleById($model->aid)['title'];
                            }
                        ],
                        [
                            'attribute' => 'nickname',
                        ],
                        [
                            'attribute' => 'content',
                            'format' => 'html',
                        ],
                        [
                            'attribute' => 'status',
                            'format' => 'html',
                            'value' => function ($model, $key, $index, $column) {
                                $text = Constants::getCommentStatusItems($model->status);
                                if ($model->status == Comment::STATUS_INIT) {
                                    $class = 'btn-default';
                                } else {
                                    if ($model->status == Comment::STATUS_PASSED) {
                                        $class = 'btn-info';
                                    } else {
                                        $class = 'btn-danger';
                                    }
                                }
                                return "<a class='btn {$class} btn-xs btn-rounded'>{$text}</a>";
                            },
                            'filter' => Constants::getCommentStatusItems(),
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
                            'width' => '135',
                            'buttons' => [
                                'change-status' => function ($url, $model, $key) {//echo $model->status;die;
                                    if ($model->status == Comment::STATUS_INIT) {
                                        return Html::a('<i class="fa fa-check"></i> ' . Yii::t('app', 'Passed'), $url . '&status=' . Comment::STATUS_PASSED, [
                                                'title' => Yii::t('app', 'Passed'),
                                                'data-pjax' => '0',
                                                'class' => 'btn btn-white btn-sm',
                                                'data-confirm' => Yii::t('app', 'Are you sure you want to enable this item?'),
                                            ]) . Html::a('<i class="fa fa-remove"></i> ' . Yii::t('app', 'Unpassed'), $url . '&status=' . Comment::STATUS_UNPASS, [
                                                'title' => Yii::t('app', 'Unpassed'),
                                                'data-pjax' => '0',
                                                'class' => 'btn btn-white btn-sm',
                                                'data-confirm' => Yii::t('app', 'Are you sure you want to disable this item?'),
                                            ]);
                                    } else {
                                        if ($model->status == Comment::STATUS_UNPASS) {
                                            return Html::a('<i class="fa fa-check"></i> ' . Yii::t('app', 'Passed'), $url . '&status=' . Comment::STATUS_PASSED, [
                                                'title' => Yii::t('app', 'Passed'),
                                                'data-pjax' => '0',
                                                'class' => 'btn btn-white btn-sm',
                                                'data-confirm' => Yii::t('app', 'Are you sure you want to enable this item?'),
                                            ]);
                                        } else {
                                            if ($model->status == Comment::STATUS_PASSED) {
                                                return Html::a('<i class="fa fa-remove"></i> ' . Yii::t('app', 'Unpassed'), $url . '&status=' . Comment::STATUS_UNPASS, [
                                                    'title' => Yii::t('app', 'Unpassed'),
                                                    'data-pjax' => '0',
                                                    'class' => 'btn btn-white btn-sm',
                                                    'data-confirm' => Yii::t('app', 'Are you sure you want to disable this item?'),
                                                ]);
                                            }
                                        }
                                    }
                                },
                            ],
                            'template' => '{change-status}{delete}',
                        ],
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>