<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-23 17:51
 */

/**
 * @var $this yii\web\View
 * @var $dataProvider backend\models\AdminLog
 * @var $searchModel backend\models\search\AdminLogSearch
 */

use backend\grid\DateColumn;
use backend\grid\GridView;
use yii\helpers\StringHelper;
use backend\widgets\Bar;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;

$this->title = "Admin Log";

$this->params['breadcrumbs'][] = yii::t('app', 'Admin Log');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget([
                    'template' => '{refresh} {delete}'
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
                            'label' => Yii::t('app', 'Admin'),
                            'attribute' => 'adminUsername',
                            'value' => 'user.username',
                        ],
                        [
                            'attribute' => 'route',
                        ],
                        [
                            'attribute' => 'description',
                            'format' => 'html',
                            'value' => function ($model, $key, $index, $column) {
                                return StringHelper::truncate($model->description, 200);
                            }
                        ],
                        [
                            'class' => DateColumn::className(),
                            'attribute' => 'created_at',
                        ],
                        [
                            'class' => ActionColumn::className(),
                            'template' => '{view-layer} {delete}'
                        ],
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>