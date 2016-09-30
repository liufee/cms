<?php
/**
 * Created by PhpStorm.
 * User: lf
 * Date: 16/4/11
 * Time: 22:11
 */
use yii\helpers\Url;
use feehi\grid\GridView;
use yii\helpers\Html;
use feehi\widgets\Bar;

$this->title = yii::t('app', 'Roles');

$assignment = function($url, $model){
    $assignPermission = Yii::t('app', 'Assign Permission');
    return Html::a('<i class="fa fa-magnet"></i> '.$assignPermission, Url::to(['assign','id'=>$model['id']]), [
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
                ])?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => "{items}\n{pager}",
                    'columns'=>[
                        [
                            'class' => 'feehi\grid\CheckboxColumn',
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
                            'class' => 'feehi\grid\ActionColumn',
                            'template' => '{assignment}{update}{delete}',
                            'buttons' => ['assignment'=>$assignment],
                        ],
                    ]
                ]);
                ?>
            </div>
        </div>
    </div>
</div>
