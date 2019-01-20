<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-23 17:51
 */

/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel backend\models\search\ArticleSearch
 */

use backend\grid\DateColumn;
use backend\grid\GridView;
use backend\grid\SortColumn;
use yii\helpers\Url;
use common\libs\Constants;
use yii\helpers\Html;
use backend\widgets\Bar;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;

$this->title = 'Pages';
$this->params['breadcrumbs'][] = Yii::t('app', 'Pages');
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
                    'columns' => [
                        [
                            'class' => CheckboxColumn::className(),
                        ],
                        [
                            'attribute' => 'id',
                        ],
                        [
                            'class' => SortColumn::className()
                        ],
                        [
                            'attribute' => 'title',
                        ],
                        [
                            'attribute' => 'sub_title',
                            'label' => Yii::t("app", "Page Sign")
                        ],
                        [
                            'attribute' => 'author_name',
                        ],
                        [
                            'label' => Yii::t('app', 'Url'),
                            'format' => 'raw',
                            'value' => function($model){
                                return "<a target='_blank' href='" . Yii::$app->params['site']['url'] . 'index.php?r=page/' . $model->sub_title . "'>" . Yii::$app->params['site']['url'] . 'index.php?r=page/' . $model->sub_title . '</a>';
                            },
                        ],
                        [
                            'attribute' => 'status',
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $column) {
                                /** @var $model backend\models\Article */
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
                            'class' => DateColumn::className(),
                            'attribute' => 'created_at',
                        ],
                        [
                            'class' => DateColumn::className(),
                            'attribute' => 'updated_at',
                        ],
                        [
                            'class' => ActionColumn::className(),
                            'buttons' => [
                                'comment' => function ($url, $model, $key) {
                                    return Html::a('<i class="fa  fa-commenting-o" aria-hidden="true"></i> ', Url::to([
                                        'comment/index',
                                        'CommentSearch[aid]' => $model->id
                                    ]), [
                                        'title' => Yii::t('app', 'Comments'),
                                        'data-pjax' => '0',
                                        'class' => 'btn-sm openContab',
                                    ]);
                                }
                            ],
                            'width' => '135',
                            'template' => '{view-layer} {update} {delete} {comment}',
                        ],
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>