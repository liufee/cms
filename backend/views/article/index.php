<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-23 17:51
 */

/**
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel backend\models\ArticleSearch
 */

use backend\grid\GridView;
use common\widgets\JsBlock;
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
$this->params['breadcrumbs'][] = yii::t('app', 'Articles');

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
                            'class' => CheckboxColumn::className(),
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
                            'filter' => Category::getCategoriesName(),
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
                                return Html::a(Constants::getYesNoItems($model['flag_headline']), ['update', 'id' => $model['id']], [
                                    'class' => 'btn btn-xs btn-rounded ' . ( $model['flag_headline'] == Constants::YesNo_Yes ? 'btn-info' : 'btn-default' ),
                                    'data-confirm' => $model['flag_headline'] == Constants::YesNo_Yes ? Yii::t('app', 'Are you sure you want to disable this item?') : Yii::t('app', 'Are you sure you want to enable this item?'),
                                    'data-method' => 'post',
                                    'data-pjax' => '0',
                                    'data-params' => [
                                        $model->formName() . '[flag_headline]' => $model['flag_headline'] == Constants::YesNo_Yes ? Constants::YesNo_No : Constants::YesNo_Yes
                                    ]
                                ]);
                            },
                        ],
                        [
                            'attribute' => 'flag_recommend',
                            'filter' => Constants::getYesNoItems(),
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $column) {
                                return Html::a(Constants::getYesNoItems($model['flag_recommend']), ['update', 'id' => $model['id']], [
                                    'class' => 'btn btn-xs btn-rounded ' . ( $model['flag_recommend'] == Constants::YesNo_Yes ? 'btn-info' : 'btn-default' ),
                                    'data-confirm' => $model['flag_recommend'] == Constants::YesNo_Yes ? Yii::t('app', 'Are you sure you want to disable this item?') : Yii::t('app', 'Are you sure you want to enable this item?'),
                                    'data-method' => 'post',
                                    'data-pjax' => '0',
                                    'data-params' => [
                                        $model->formName() . '[flag_recommend]' => $model['flag_recommend'] == Constants::YesNo_Yes ? Constants::YesNo_No : Constants::YesNo_Yes
                                    ]
                                ]);
                            },
                        ],
                        [
                            'attribute' => 'flag_slide_show',
                            'filter' => Constants::getYesNoItems(),
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $column) {
                                return Html::a(Constants::getYesNoItems($model['flag_slide_show']), ['update', 'id' => $model['id']], [
                                    'class' => 'btn btn-xs btn-rounded ' . ( $model['flag_slide_show'] == Constants::YesNo_Yes ? 'btn-info' : 'btn-default' ),
                                    'data-confirm' => $model['flag_slide_show'] == Constants::YesNo_Yes ? Yii::t('app', 'Are you sure you want to disable this item?') : Yii::t('app', 'Are you sure you want to enable this item?'),
                                    'data-method' => 'post',
                                    'data-pjax' => '0',
                                    'data-params' => [
                                        $model->formName() . '[flag_slide_show]' => $model['flag_slide_show'] == Constants::YesNo_Yes ? Constants::YesNo_No : Constants::YesNo_Yes
                                    ]
                                ]);
                            },
                        ],
                        [
                            'attribute' => 'flag_special_recommend',
                            'filter' => Constants::getYesNoItems(),
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $column) {
                                return Html::a(Constants::getYesNoItems($model['flag_special_recommend']), ['update', 'id' => $model['id']], [
                                    'class' => 'btn btn-xs btn-rounded ' . ( $model['flag_special_recommend'] == Constants::YesNo_Yes ? 'btn-info' : 'btn-default' ),
                                    'data-confirm' => $model['flag_special_recommend'] == Constants::YesNo_Yes ? Yii::t('app', 'Are you sure you want to disable this item?') : Yii::t('app', 'Are you sure you want to enable this item?'),
                                    'data-method' => 'post',
                                    'data-pjax' => '0',
                                    'data-params' => [
                                        $model->formName() . '[flag_special_recommend]' => $model['flag_special_recommend'] == Constants::YesNo_Yes ? Constants::YesNo_No : Constants::YesNo_Yes
                                    ]
                                ]);
                            },
                        ],
                        [
                            'attribute' => 'flag_roll',
                            'filter' => Constants::getYesNoItems(),
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $column) {
                                return Html::a(Constants::getYesNoItems($model['flag_roll']), ['update', 'id' => $model['id']], [
                                    'class' => 'btn btn-xs btn-rounded ' . ( $model['flag_roll'] == Constants::YesNo_Yes ? 'btn-info' : 'btn-default' ),
                                    'data-confirm' => $model['flag_roll'] == Constants::YesNo_Yes ? Yii::t('app', 'Are you sure you want to disable this item?') : Yii::t('app', 'Are you sure you want to enable this item?'),
                                    'data-method' => 'post',
                                    'data-pjax' => '0',
                                    'data-params' => [
                                        $model->formName() . '[flag_roll]' => $model['flag_roll'] == Constants::YesNo_Yes ? Constants::YesNo_No : Constants::YesNo_Yes
                                    ]
                                ]);
                            },
                        ],
                        [
                            'attribute' => 'flag_bold',
                            'filter' => Constants::getYesNoItems(),
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $column) {
                                return Html::a(Constants::getYesNoItems($model['flag_bold']), ['update', 'id' => $model['id']], [
                                    'class' => 'btn btn-xs btn-rounded ' . ( $model['flag_bold'] == Constants::YesNo_Yes ? 'btn-info' : 'btn-default' ),
                                    'data-confirm' => $model['flag_bold'] == Constants::YesNo_Yes ? Yii::t('app', 'Are you sure you want to disable this item?') : Yii::t('app', 'Are you sure you want to enable this item?'),
                                    'data-method' => 'post',
                                    'data-pjax' => '0',
                                    'data-params' => [
                                        $model->formName() . '[flag_bold]' => $model['flag_bold'] == Constants::YesNo_Yes ? Constants::YesNo_No : Constants::YesNo_Yes
                                    ]
                                ]);
                            },
                        ],
                        [
                            'attribute' => 'flag_picture',
                            'filter' => Constants::getYesNoItems(),
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $column) {
                                return Html::a(Constants::getYesNoItems($model['flag_picture']), ['update', 'id' => $model['id']], [
                                    'class' => 'btn btn-xs btn-rounded ' . ( $model['flag_picture'] == Constants::YesNo_Yes ? 'btn-info' : 'btn-default' ),
                                    'data-confirm' => $model['flag_picture'] == Constants::YesNo_Yes ? Yii::t('app', 'Are you sure you want to disable this item?') : Yii::t('app', 'Are you sure you want to enable this item?'),
                                    'data-method' => 'post',
                                    'data-pjax' => '0',
                                    'data-params' => [
                                        $model->formName() . '[flag_picture]' => $model['flag_picture'] == Constants::YesNo_Yes ? Constants::YesNo_No : Constants::YesNo_Yes
                                    ]
                                ]);
                            },
                        ],
                        [
                            'attribute' => 'status',
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $column) {
                                return Html::a(Constants::getArticleStatus($model['status']), ['update', 'id' => $model['id']], [
                                    'class' => 'btn btn-xs btn-rounded ' . ( $model['status'] == Constants::YesNo_Yes ? 'btn-info' : 'btn-default' ),
                                    'data-confirm' => $model['status'] == Constants::YesNo_Yes ? Yii::t('app', 'Are you sure you want to cancel release?') : Yii::t('app', 'Are you sure you want to publish?'),
                                    'data-method' => 'post',
                                    'data-pjax' => '0',
                                    'data-params' => [
                                        $model->formName() . '[status]' => $model['status'] == Constants::YesNo_Yes ? Constants::YesNo_No : Constants::YesNo_Yes
                                    ]
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
                                ]) . Html::activeInput('text', $searchModel, 'create_end_at', [
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
                                ]) . Html::activeInput('text', $searchModel, 'update_end_at', [
                                    'class' => 'form-control layer-date',
                                    'placeholder' => '',
                                    'onclick' => "laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})"
                                ]),
                        ],
                        [
                            'class' => ActionColumn::className(),
                            'buttons' => [
                                'comment' => function ($url, $model, $key) {
                                    return Html::a('<i class="fa  fa-commenting-o" aria-hidden="true"></i> ' . Yii::t('app', 'Comments'), Url::to([
                                        'comment/index',
                                        'CommentSearch[aid]' => $model->id
                                    ]), [
                                        'title' => Yii::t('app', 'Comments'),
                                        'data-pjax' => '0',
                                        'class' => 'btn btn-white btn-sm openContab',
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
<?php JsBlock::begin()?>
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
</script>
<?php JsBlock::end()?>