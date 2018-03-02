<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-21 14:14
 */

/**
 * @var $this yii\web\View
 * @var $dataProvider frontend\models\Menu
 */

use backend\grid\DateColumn;
use backend\grid\GridView;
use backend\grid\SortColumn;
use backend\grid\StatusColumn;
use backend\widgets\Bar;
use frontend\models\Menu;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;

$this->title = "Frontend Menus";
$this->params['breadcrumbs'][] = yii::t('app', 'Frontend Menus');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget() ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => '{items}',
                    'columns' => [
                        [
                            'class' => CheckboxColumn::className(),
                        ],
                        [
                            'attribute' => 'name',
                            'label' => yii::t('app', 'Name'),
                            'format' => 'html',
                            'value' => function ($model, $key, $index, $column) {
                                $return = '';
                                for ($i = 0; $i < $model['level']; $i++) {
                                    $return .= "&nbsp;&nbsp;&nbsp;&nbsp;";
                                }
                                return $return . $model['name'];
                            }
                        ],
                        [
                            'attribute' => 'icon',
                            'label' => yii::t('app', 'Icon'),
                            'format' => 'html',
                            'value' => function ($model) {
                                return "<i class=\"fa {$model['icon']}\"></i>";
                            }
                        ],
                        [
                            'attribute' => 'url',
                            'label' => yii::t('app', 'Url'),
                        ],
                        [
                            'class' => SortColumn::className(),
                            'label' => yii::t('app', 'Sort')
                        ],
                        [
                            'attribute' => 'is_display',
                            'class' => StatusColumn::className(),
                            'label' => yii::t('app', 'Is Display'),
                            'formName' => (new Menu)->formName() . '[is_display]',
                        ],
                        [
                            'class' => DateColumn::className(),
                            'attribute' => 'created_at',
                            'label' => yii::t('app', 'Created At'),
                        ],
                        [
                            'class' => DateColumn::className(),
                            'attribute' => 'updated_at',
                            'label' => yii::t('app', 'Updated At'),
                        ],
                        [
                            'class' => ActionColumn::className(),
                            'buttons' => [
                                'create' => function ($url, $model, $key) {
                                    return Html::a('<i class="fa  fa-plus" aria-hidden="true"></i> ' . Yii::t('app', 'Create'), Url::to([
                                        'create',
                                        'parent_id' => $model['id']
                                    ]), [
                                        'title' => Yii::t('app', 'Create'),
                                        'data-pjax' => '0',
                                        'class' => 'btn btn-white btn-sm J_menuItem',
                                    ]);
                                }
                            ],
                            'template' => '{create} {view-layer} {update} {delete}',
                            'width' => '190px'
                        ]
                    ]
                ]) ?>
            </div>
        </div>
    </div>
</div>