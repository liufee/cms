<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-21 14:14
 */

/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

use backend\grid\DateColumn;
use backend\grid\GridView;
use backend\widgets\Bar;
use yii\helpers\Html;
use backend\models\FriendLink;
use yii\helpers\Url;
use common\libs\Constants;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;

$this->title = "Friendly Links";
$this->params['breadcrumbs'][] = yii::t('app', 'Friendly Links');
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
                            'class' => CheckboxColumn::className(),
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
                                return Html::a(Constants::getYesNoItems($model['status']), ['update', 'id' => $model['id']], [
                                    'class' => 'btn btn-xs btn-rounded ' . ( $model['status'] == FriendLink::DISPLAY_YES ? 'btn-info' : 'btn-default' ),
                                    'data-confirm' => $model['status'] == FriendLink::DISPLAY_YES ? Yii::t('app', 'Are you sure you want to disable this item?') : Yii::t('app', 'Are you sure you want to enable this item?'),
                                    'data-method' => 'post',
                                    'data-pjax' => '0',
                                    'data-params' => [
                                        $model->formName() . '[status]' => $model['status'] == FriendLink::DISPLAY_YES ? FriendLink::DISPLAY_NO : FriendLink::DISPLAY_YES
                                    ]
                                ]);
                            },
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
                        ]
                    ]
                ]) ?>
            </div>
        </div>
    </div>
</div>