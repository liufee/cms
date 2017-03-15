<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-23 17:51
 */

use backend\grid\GridView;
use yii\helpers\Url;
use common\models\Category;
use common\libs\Constants;
use yii\helpers\Html;
use backend\widgets\Bar;
use common\widgets\Pjax;
use backend\models\Article;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;

$this->title = 'Articles';

?>
    <style>
        select.form-control {
            padding: 0px
        }
    </style>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <?= $this->render('/widgets/_ibox-title') ?>
                <div class="ibox-content">
                    <?= Bar::widget() ?>
                    <?php Pjax::begin(['id' => 'pjax']); ?>
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
                                'attribute' => 'cid',
                                'label' => yii::t('app', 'Category'),
                                'value' => function ($model) {
                                    return $model->category ? $model->category->name : yii::t('app', 'uncategoried');
                                },
                                'filter' => Category::getType(),
                            ],
                            [
                                'attribute' => 'sort',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return \yii\helpers\Html::input('number', "sort[{$model['id']}]", $model['sort'], ['style' => 'width:50px']);
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
                                'attribute' => 'thumb',
                                'filter' => Constants::getYesNoItems(),
                                'value' => function ($model, $key, $index, $column) {
                                    if ($model->thumb == '') {
                                        $num = 0;
                                    } else {
                                        $num = 1;
                                    }
                                    return Constants::getYesNoItems($num);
                                },
                            ],
                            [
                                'attribute' => 'flag_headline',
                                'filter' => Constants::getYesNoItems(),
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    if ($model->flag_headline) {
                                        $url = Url::to([
                                            'change-status',
                                            'id' => $model->id,
                                            'status' => 0,
                                            'field' => 'flag_headline'
                                        ]);
                                        $class = 'btn btn-info btn-xs btn-rounded';
                                        $confirm = Yii::t('app', 'Are you sure you want to disable this item?');
                                    } else {
                                        $url = Url::to([
                                            'change-status',
                                            'id' => $model->id,
                                            'status' => 1,
                                            'field' => 'flag_headline'
                                        ]);
                                        $class = 'btn btn-default btn-xs btn-rounded';
                                        $confirm = Yii::t('app', 'Are you sure you want to enable this item?');
                                    }
                                    return Html::a(Constants::getYesNoItems($model->flag_headline), $url, [
                                        'class' => $class,
                                        'data-confirm' => $confirm,
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]);
                                },
                            ],
                            [
                                'attribute' => 'flag_recommend',
                                'filter' => Constants::getYesNoItems(),
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    if ($model->flag_recommend) {
                                        $url = Url::to([
                                            'change-status',
                                            'id' => $model->id,
                                            'status' => 0,
                                            'field' => 'flag_recommend'
                                        ]);
                                        $class = 'btn btn-info btn-xs btn-rounded';
                                        $confirm = Yii::t('app', 'Are you sure you want to disable this item?');
                                    } else {
                                        $url = Url::to([
                                            'change-status',
                                            'id' => $model->id,
                                            'status' => 1,
                                            'field' => 'flag_recommend'
                                        ]);
                                        $class = 'btn btn-default btn-xs btn-rounded';
                                        $confirm = Yii::t('app', 'Are you sure you want to enable this item?');
                                    }
                                    return Html::a(Constants::getYesNoItems($model->flag_recommend), $url, [
                                        'class' => $class,
                                        'data-confirm' => $confirm,
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]);
                                },
                            ],
                            [
                                'attribute' => 'flag_slide_show',
                                'filter' => Constants::getYesNoItems(),
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    if ($model->flag_slide_show) {
                                        $url = Url::to([
                                            'change-status',
                                            'id' => $model->id,
                                            'status' => 0,
                                            'field' => 'flag_slide_show'
                                        ]);
                                        $class = 'btn btn-info btn-xs btn-rounded';
                                        $confirm = Yii::t('app', 'Are you sure you want to disable this item?');
                                    } else {
                                        $url = Url::to([
                                            'change-status',
                                            'id' => $model->id,
                                            'status' => 1,
                                            'field' => 'flag_slide_show'
                                        ]);
                                        $class = 'btn btn-default btn-xs btn-rounded';
                                        $confirm = Yii::t('app', 'Are you sure you want to enable this item?');
                                    }
                                    return Html::a(Constants::getYesNoItems($model->flag_slide_show), $url, [
                                        'class' => $class,
                                        'data-confirm' => $confirm,
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]);
                                },
                            ],
                            [
                                'attribute' => 'flag_special_recommend',
                                'filter' => Constants::getYesNoItems(),
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    if ($model->flag_special_recommend) {
                                        $url = Url::to([
                                            'change-status',
                                            'id' => $model->id,
                                            'status' => 0,
                                            'field' => 'flag_special_recommend'
                                        ]);
                                        $class = 'btn btn-info btn-xs btn-rounded';
                                        $confirm = Yii::t('app', 'Are you sure you want to disable this item?');
                                    } else {
                                        $url = Url::to([
                                            'change-status',
                                            'id' => $model->id,
                                            'status' => 1,
                                            'field' => 'flag_special_recommend'
                                        ]);
                                        $class = 'btn btn-default btn-xs btn-rounded';
                                        $confirm = Yii::t('app', 'Are you sure you want to enable this item?');
                                    }
                                    return Html::a(Constants::getYesNoItems($model->flag_special_recommend), $url, [
                                        'class' => $class,
                                        'data-confirm' => $confirm,
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]);
                                },
                            ],
                            [
                                'attribute' => 'flag_roll',
                                'filter' => Constants::getYesNoItems(),
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    if ($model->flag_roll) {
                                        $url = Url::to([
                                            'change-status',
                                            'id' => $model->id,
                                            'status' => 0,
                                            'field' => 'flag_roll'
                                        ]);
                                        $class = 'btn btn-info btn-xs btn-rounded';
                                        $confirm = Yii::t('app', 'Are you sure you want to disable this item?');
                                    } else {
                                        $url = Url::to([
                                            'change-status',
                                            'id' => $model->id,
                                            'status' => 1,
                                            'field' => 'flag_roll'
                                        ]);
                                        $class = 'btn btn-default btn-xs btn-rounded';
                                        $confirm = Yii::t('app', 'Are you sure you want to enable this item?');
                                    }
                                    return Html::a(Constants::getYesNoItems($model->flag_roll), $url, [
                                        'class' => $class,
                                        'data-confirm' => $confirm,
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]);
                                },
                            ],
                            [
                                'attribute' => 'flag_bold',
                                'filter' => Constants::getYesNoItems(),
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    if ($model->flag_bold) {
                                        $url = Url::to([
                                            'change-status',
                                            'id' => $model->id,
                                            'status' => 0,
                                            'field' => 'flag_bold'
                                        ]);
                                        $class = 'btn btn-info btn-xs btn-rounded';
                                        $confirm = Yii::t('app', 'Are you sure you want to disable this item?');
                                    } else {
                                        $url = Url::to([
                                            'change-status',
                                            'id' => $model->id,
                                            'status' => 1,
                                            'field' => 'flag_bold'
                                        ]);
                                        $class = 'btn btn-default btn-xs btn-rounded';
                                        $confirm = Yii::t('app', 'Are you sure you want to enable this item?');
                                    }
                                    return Html::a(Constants::getYesNoItems($model->flag_bold), $url, [
                                        'class' => $class,
                                        'data-confirm' => $confirm,
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]);
                                },
                            ],
                            [
                                'attribute' => 'flag_picture',
                                'filter' => Constants::getYesNoItems(),
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    if ($model->flag_picture) {
                                        $url = Url::to([
                                            'change-status',
                                            'id' => $model->id,
                                            'status' => 0,
                                            'field' => 'flag_picture'
                                        ]);
                                        $class = 'btn btn-info btn-xs btn-rounded';
                                        $confirm = Yii::t('app', 'Are you sure you want to disable this item?');
                                    } else {
                                        $url = Url::to([
                                            'change-status',
                                            'id' => $model->id,
                                            'status' => 1,
                                            'field' => 'flag_picture'
                                        ]);
                                        $class = 'btn btn-default btn-xs btn-rounded';
                                        $confirm = Yii::t('app', 'Are you sure you want to enable this item?');
                                    }
                                    return Html::a(Constants::getYesNoItems($model->flag_picture), $url, [
                                        'class' => $class,
                                        'data-confirm' => $confirm,
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]);
                                },
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
                                'template' => '{view-layer} {update} {delete}{comment}',
                            ],
                        ]
                    ]); ?>
                    <?php Pjax::end(); ?>
                </div>
            </div>
        </div>
    </div>
    <script>
        function showImg() {
            t = setTimeout(function () {
            }, 200);
            var node = $(this).attr('title');
            if (node.length == 0) {
                layer.tips('<?=yii::t('app', 'No picture')?>', $(this));
            } else {
                layer.tips('<img src=' + node + '>', $(this));
            }
        }
    </script>
<?php $this->registerJs("
$(document).ready(function(){
    var t;
    $('table tr td a.title').hover(showImg,function(){
       clearTimeout(t);
    });
});
var container = $('#pjax');
container.on('pjax:send',function(args){
    layer.load(2);
});
container.on('pjax:complete',function(args){
    layer.closeAll('loading');
    $('table tr td a.title').bind('mouseover mouseout', showImg);
});
")
?>