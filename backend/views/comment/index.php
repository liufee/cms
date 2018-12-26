<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-23 17:51
 */

/**
 * @var $this yii\web\View
 * @var $dataProvider backend\models\Comment
 * @var $searchModel backend\models\search\CommentSearch
 */

use backend\grid\DateColumn;
use backend\grid\GridView;
use common\libs\Constants;
use yii\helpers\Html;
use backend\widgets\Bar;
use backend\models\Comment;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;

$this->title = 'Comments';
$this->params['breadcrumbs'][] = Yii::t('app', 'Comments');
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
                    'columns' => [
                        [
                            'class' => CheckboxColumn::className(),
                        ],
                        [
                            'attribute' => 'id',
                        ],
                        [
                            'attribute' => 'aid',
                        ],
                        [
                            'attribute' => 'articleTitle',
                            'label' => Yii::t('app', 'Article Title'),
                            'value' => function ($model) {
                                return $model->article->title;
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
                            'class' => DateColumn::className(),
                            'attribute' => 'created_at',
                        ],
                        [
                            'class' => DateColumn::className(),
                            'attribute' => 'updated_at',
                        ],
                        [
                            'class' => ActionColumn::className(),
                            'width' => '135',
                            'buttons' => [
                                'status_init' => function($url, $model, $key){
                                    $comment = new Comment();
                                    if( $model->status != Comment::STATUS_INIT ) return '';
                                    return Html::a('<i class="fa fa-check"></i> ', ['update', 'id' => $model['id']], [
                                            'class' => 'btn-sm',
                                            'data-confirm' => Yii::t('app', 'Are you sure you want to enable this item?'),
                                            'data-method' => 'post',
                                            'data-pjax' => '0',
                                            'data-params' => [
                                                $comment->formName() . '[status]' => Comment::STATUS_PASSED
                                            ]
                                        ]) . Html::a('<i class="fa fa-remove"></i> ', ['update', 'id' => $model['id']], [
                                            'class' => 'btn-sm',
                                            'data-confirm' => Yii::t('app', 'Are you sure you want to disable this item?'),
                                            'data-method' => 'post',
                                            'data-pjax' => '0',
                                            'data-params' => [
                                                $comment->formName() . '[status]' => Comment::STATUS_UNPASS
                                            ]
                                        ]);
                                },
                                'status_operated' => function ($url, $model, $key) {
                                    if( $model->status == Comment::STATUS_INIT ) return '';
                                    $comment = new Comment();
                                    if ($model->status == Comment::STATUS_PASSED ) {
                                        return Html::a('<i class="fa fa-remove"></i> ', ['update', 'id' => $model['id']], [
                                            'class' => 'btn-sm',
                                            'data-confirm' => Yii::t('app', 'Are you sure you want to enable this item?'),
                                            'data-method' => 'post',
                                            'data-pjax' => '0',
                                            'data-params' => [
                                                $comment->formName() . '[status]' => Comment::STATUS_UNPASS
                                            ]
                                        ]);
                                    } else if( $model->status == Comment::STATUS_UNPASS ) {
                                        return Html::a('<i class="fa fa-check"></i> ', ['update', 'id' => $model['id']], [
                                            'class' => 'btn-sm',
                                            'data-confirm' => Yii::t('app', 'Are you sure you want to disable this item?'),
                                            'data-method' => 'post',
                                            'data-pjax' => '0',
                                            'data-params' => [
                                                $comment->formName() . '[status]' => Comment::STATUS_PASSED
                                            ]
                                        ]);
                                    }
                                },
                            ],
                            'template' => '{view-layer} {status_init} {status_operated} {update} {delete}',
                        ],
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>