<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-12-03 21:38
 */
/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ArrayDataProvider
 */

use backend\grid\GridView;
use backend\widgets\Bar;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;

$this->title = "Banner Types";
$this->params['breadcrumbs'][] = yii::t('app', 'Banner Types');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget()?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => '{items}',
                    'columns' => [
                        [
                            'class' => CheckboxColumn::className(),
                        ],
                        [
                            'attribute' => 'name',
                        ],
                        [
                            'attribute' => 'tips',
                        ],
                        [
                            'class' => ActionColumn::className(),
                            'width' => '190px',
                            'buttons' => [
                                'entry' => function ($url, $model, $key) {
                                    return Html::a('<i class="fa fa-bars" aria-hidden="true"></i> ' . Yii::t('app', 'Entry'), Url::to([
                                        'banners',
                                        'id' => $model['id']
                                    ]), [
                                        'title' => Yii::t('app', 'Entry'),
                                        'data-pjax' => '0',
                                        'class' => 'btn btn-white btn-sm J_menuItem',
                                    ]);
                                }
                            ],
                            'template' => '{entry} {update} {delete}',
                        ]
                    ]
                ]) ?>
            </div>
        </div>
    </div>
</div>