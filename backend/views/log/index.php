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
use feehi\libs\Help;
use feehi\widgets\Bar;

$this->title = "Admin Log";
?>
<div class="row" xmlns="http://www.w3.org/1999/html">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title', [
                'buttons' => [
                    [
                        'name' => 'Delete',
                        'url' => ['delete'],
                        'options' => [
                            'class' => 'multi-operate btn btn-primary btn-xs',
                            'data-confirm' => yii::t('app', 'Realy to delete?'),
                        ]
                    ]
                ]
            ]) ?>
            <div class="ibox-content">
                <?= Bar::widget([
                    'template' => '{refresh} {create} {delete}'
                ]) ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns'=>[
                        [
                            'class' => 'feehi\grid\CheckboxColumn',
                        ],
                        [
                            'attribute' => 'id',
                        ],
                        [
                            'label' => Yii::t('app', 'Admin'),
                            'attribute' => 'user_username',
                            'value' => 'user.username',
                            'filter'=>Html::activeTextInput($searchModel, 'user_username',['class'=>'form-control'])
                        ],
                        [
                            'attribute' => 'route',
                        ],
                        [
                            'attribute' => 'description',
                            'format' => 'html',
                            'value' => function($model, $key, $index, $column){
                                return Help::truncate_utf8_string($model->description, '200')."<a class='detail'>更多</a>";
                            }
                        ],
                        [
                            'attribute' => 'created_at',
                            'format' => ['date'],
                            'filter' => Html::activeInput('text', $searchModel, 'create_start_at', ['class'=>'form-control layer-date', 'placeholder'=>'', 'onclick'=>"laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'});"]).\yii\helpers\Html::activeInput('text', $searchModel, 'create_end_at', ['class'=>'form-control layer-date', 'placeholder'=>'', 'onclick'=>"laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})"]),
                        ],
                        [
                            'class' => 'feehi\grid\ActionColumn',
                            'template' => '{delete}'
                        ],
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>
<?php
    $url = Url::to(['view']);
    $logDetail = yii::t('app', 'Log Detail');
    $this->registerJs(
    "$('.detail').on('click', function(){
            var id = $(this).parents('tr:first').attr('data-key');
            var url = '{$url}'+'&id='+id;
            layer.open({
                type: 2,
                title: '{$logDetail}',
                maxmin: true,
                shadeClose: true, //点击遮罩关闭层
                area : ['800px' , '520px'],
                content: url
            });
        });"
    );