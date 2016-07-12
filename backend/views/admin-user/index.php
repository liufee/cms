<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/23
 * Time: 17:51
 */
use feehi\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use backend\models\AdminRoles;
use feehi\widgets\Bar;

$assignment = function($url, $model){
    return Html::a('<i class="fa fa-tablet"></i> '.yii::t('app', 'Assign Roles'), Url::to(['assign','uid'=>$model['id']]), [
        'title' => 'assignment',
        'class' => 'btn btn-white btn-sm'
    ]);
};

$this->title = "Admin";
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget([
                    'buttons' => [
                        [
                            'class' => 'btn btn-white btn-sm refresh',
                            'text' => 'Refresh',
                            'url' => ['refresh'],
                            'iClass' => 'fa fa-refresh',
                        ],
                        [
                            'class' => 'btn btn-white btn-sm',
                            'text' => 'Create',
                            'url' => ['create'],
                            'iClass' => 'fa fa-plus',
                        ],
                        [
                            'class' => 'btn btn-white btn-sm multi-delete',
                            'text' => 'Delete',
                            'url' => ['delete'],
                            'iClass' => 'fa fa-trash-o',
                        ],
                    ]
                ])?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    //'filterModel' => $searchModel,
                    //'layout' => "{items}\n{pager}",
                    'columns'=>[
                        [
                            'class' => 'feehi\grid\CheckboxColumn',
                        ],
                        [
                            'attribute' => 'username',
                        ],
                        [
                            'attribute' => 'role',
                            'label' => yii::t('app', 'Role'),
                            'value' => function($model){
                                return AdminRoles::getRoleNameByUid($model->id);
                            },
                        ],
                        [
                            'attribute' => 'email',
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
                ]); ?>
            </div>
        </div>
    </div>
</div>