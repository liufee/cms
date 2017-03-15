<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-21 14:14
 */

use backend\grid\GridView;
use backend\widgets\Bar;
use yii\helpers\Html;
use backend\models\FriendLink;
use yii\helpers\Url;
use common\libs\Constants;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;

$this->title = "Friendly Links";
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget() ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        [
                            'class' => CheckboxColumn::class,
                        ],
                        [
                            'attribute' => 'name'
                        ],
                        [
                            'attribute' => 'url',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return Html::a($model->url, $model->url, ['target' => '_blank']);
                            }
                        ],
                        [
                            'attribute' => 'sort',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return Html::input('number', "sort[{$model['id']}]", $model['sort']);
                            }
                        ],
                        [
                            'attribute' => 'status',
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $column) {
                                if ($model->status == FriendLink::DISPLAY_YES) {
                                    $url = Url::to([
                                        'change-status',
                                        'id' => $model->id,
                                        'status' => 0,
                                        'field' => 'status'
                                    ]);
                                    $class = 'btn btn-info btn-xs btn-rounded';
                                    $confirm = Yii::t('app', 'Are you sure you want to disable this item?');
                                } else {
                                    $url = Url::to([
                                        'change-status',
                                        'id' => $model->id,
                                        'status' => 1,
                                        'field' => 'status'
                                    ]);
                                    $class = 'btn btn-default btn-xs btn-rounded';
                                    $confirm = Yii::t('app', 'Are you sure you want to enable this item?');
                                }
                                return Html::a(Constants::getYesNoItems($model->status), $url, [
                                    'class' => $class,
                                    'data-confirm' => $confirm,
                                    'data-method' => 'post',
                                    'data-pjax' => '0',
                                ]);

                            },
                        ],
                        [
                            'attribute' => 'created_at',
                            'format' => 'date',
                        ],
                        [
                            'attribute' => 'updated_at',
                            'format' => 'date',
                        ],
                        [
                            'class' => ActionColumn::class,
                        ]
                    ]
                ]) ?>
            </div>
        </div>
    </div>
</div>