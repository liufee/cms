<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-23 17:51
 */

use backend\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use common\helpers\StringHelper;
use backend\widgets\Bar;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;

$this->title = "Admin Log";
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
                                'class' => CheckboxColumn::class,
                            ],
                            [
                                'attribute' => 'id',
                            ],
                            [
                                'label' => Yii::t('app', 'Admin'),
                                'attribute' => 'user_username',
                                'value' => 'user.username',
                                'filter' => Html::activeTextInput($searchModel, 'user_username', ['class' => 'form-control'])
                            ],
                            [
                                'attribute' => 'route',
                            ],
                            [
                                'attribute' => 'description',
                                'format' => 'html',
                                'value' => function ($model, $key, $index, $column) {
                                    return StringHelper::truncate_utf8_string($model->description, '200') . "<a class='detail'>" . yii::t('app', 'more') . "...</a>";
                                }
                            ],
                            [
                                'attribute' => 'created_at',
                                'format' => ['date'],
                                'filter' => Html::activeInput('text', $searchModel, 'create_start_at', [
                                        'class' => 'form-control layer-date',
                                        'placeholder' => '',
                                        'onclick' => "laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'});"
                                    ]) . \yii\helpers\Html::activeInput('text', $searchModel, 'create_end_at', [
                                        'class' => 'form-control layer-date',
                                        'placeholder' => '',
                                        'onclick' => "laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})"
                                    ]),
                            ],
                            [
                                'class' => ActionColumn::class,
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
$this->registerJs("$('.detail').on('click', function(){
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
        });");