<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-11 22:11
 */

use yii\helpers\Url;
use backend\grid\GridView;
use yii\helpers\Html;
use backend\widgets\Bar;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;

$this->title = yii::t('app', 'Roles');

$assignment = function ($url, $model) {
    $assignPermission = Yii::t('app', 'Assign Permission');
    return Html::a('<i class="fa fa-magnet"></i> ' . $assignPermission, Url::to(['assign', 'id' => $model['id']]), [
        'title' => 'assignment',
        'class' => 'btn btn-white btn-sm'
    ]);
};
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget([
                    'template' => '{refresh} {create} {delete}'
                ]) ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => "{items}\n{pager}",
                    'columns' => [
                        [
                            'class' => CheckboxColumn::class,
                        ],
                        [
                            'attribute' => 'role_name',
                        ],
                        [
                            'attribute' => 'remark',
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
                            'template' => '{assignment}{update}{delete}',
                            'buttons' => ['assignment' => $assignment],
                        ],
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>
