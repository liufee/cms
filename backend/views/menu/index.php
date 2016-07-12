<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/21
 * Time: 14:14
 */
use feehi\grid\GridView;
use yii\helpers\Url;
use feehi\widgets\Bar;

$this->title = "Backend Menus";
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget()?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        [
                            'class' => 'feehi\grid\CheckboxColumn',
                        ],
                        [
                            'attribute' => yii::t('app', 'Name'),
                            'format' => 'html',
                            'value' => function($model,$key,$index,$column){
                                $return = '';
                                for($i=0; $i<$model['level']; $i++){
                                    $return .= "&nbsp;&nbsp;&nbsp;&nbsp;";
                                }
                                $name = yii::t('menu', $model['name']);
                                return $return.$name;
                            }
                        ],
                        [
                            'attribute' => yii::t('app', 'Icon'),
                            'format' => 'html',
                            'value' => function($model){
                                return "<i class=\"fa {$model['icon']}\"></i>";
                            }
                        ],
                        [
                            'attribute' => yii::t('app', 'Url'),
                            'value' => function($model){
                                return $model['url'];
                            }
                        ],
                        [
                            'attribute' => yii::t('app', 'Sort'),
                            'format' => 'raw',
                            'value' => function($model){
                                return \yii\helpers\Html::input('number', "sort[{$model['id']}]", $model['sort']);
                            }
                        ],
                        [
                            'attribute' => yii::t('app', 'Is Display'),
                            'format' => 'html',
                            'value' => function($model){
                                if($model['is_display']){
                                    return "<a class=\"btn btn-info btn-xs btn-rounded\" href=\"javascript:void(0)\">显示</a>";
                                }else{
                                    return "<a class=\"btn btn-default btn-xs btn-rounded\" href=\"javacript:void(0)\">隐藏</a>";
                                }
                            }
                        ],
                        [
                            'attribute' => 'created_at',
                            'label' => yii::t('app', 'Created At'),
                            'format' => 'date'
                        ],
                        [
                            'attribute' => 'updated_at',
                            'label' => yii::t('app', 'Updated At'),
                            'format' => 'date',
                        ],
                        [
                            'class' => 'feehi\grid\ActionColumn',
                        ]
                    ]
                ])
                ?>
            </div>
        </div>
    </div>
</div>
<?php
    $url = Url::to(['update']);
    $this->registerJs("
        $('input[name=sort]').blur(function(){
            var val = $(this).val();
            if( isNaN(val) ){
                alert('必须为数字');
                return false;
            }
            var url = '{$url}';
            $.ajax({
                url:url,
                method:'get',
                data:{id:1,val:val},
                success:function(data){
                    alert(data);
                }
            })
        });
    ");