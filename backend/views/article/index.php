<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/23
 * Time: 17:51
 */
use feehi\grid\GridView;
use yii\helpers\Url;
use common\models\Category;
use feehi\libs\Constants;
use yii\helpers\Html;
use feehi\widgets\Bar;
use yii\widgets\Pjax;

$this->title = 'Articles';

?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget()?>
                <?php Pjax::begin(['id'=>'countries']);?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'layout' => "{items}\n{pager}",
                    'columns'=>[
                        [
                            'class' => 'feehi\grid\CheckboxColumn',
                        ],
                        [
                            'attribute' => 'id',
                        ],
                        [
                            'attribute' => 'cid',
                            'label' => yii::t('app', 'Category'),
                            'value' => function($model){
                                return Category::getTypeText($model->cid);
                            },
                            'filter' => Category::getType(),
                        ],
                        [
                            'attribute' => 'sort',
                            'format' => 'raw',
                            'value' => function($model){
                                return \yii\helpers\Html::input('number', "sort[{$model['id']}]", $model['sort'], ['style'=>'width:50px']);
                            }
                        ],
                        [
                            'attribute' => 'title',
                            'format' => 'html',
                            'width' => '170',
                            'value' => function($model, $key, $index, $column){
                                return Html::a($model->title, 'javascript:void(0)', ['title'=>$model->thumb, 'class'=>'title']);
                            }
                        ],
                        [
                            'attribute' => 'author_name',
                        ],
                        [
                            'attribute' => 'flag_headline',
                            'filter' => Constants::getYesNoItems(),
                            'value' => function($model, $key, $index, $column) {
                                return Constants::getYesNoItems($model->flag_headline);
                            },
                        ],
                        [
                            'attribute' => 'flag_recommend',
                            'filter' => Constants::getYesNoItems(),
                            'value' => function($model, $key, $index, $column) {
                                return Constants::getYesNoItems($model->flag_recommend);
                            },
                        ],
                        [
                            'attribute' => 'flag_slide_show',
                            'filter' => Constants::getYesNoItems(),
                            'value' => function($model, $key, $index, $column) {
                                return Constants::getYesNoItems($model->flag_slide_show);
                            },
                        ],
                        [
                            'attribute' => 'flag_special_recommend',
                            'filter' => Constants::getYesNoItems(),
                            'value' => function($model, $key, $index, $column) {
                                return Constants::getYesNoItems($model->flag_special_recommend);
                            },
                        ],
                        [
                            'attribute' => 'flag_roll',
                            'filter' => Constants::getYesNoItems(),
                            'value' => function($model, $key, $index, $column) {
                                return Constants::getYesNoItems($model->flag_roll);
                            },
                        ],
                        [
                            'attribute' => 'flag_bold',
                            'filter' => Constants::getYesNoItems(),
                            'value' => function($model, $key, $index, $column) {
                                return Constants::getYesNoItems($model->flag_bold);
                            },
                        ],
                        [
                            'attribute' => 'flag_picture',
                            'filter' => Constants::getYesNoItems(),
                            'value' => function($model, $key, $index, $column) {
                                return Constants::getYesNoItems($model->flag_picture);
                            },
                        ],
                        [
                            'attribute' => 'status',
                            'value' => function($model, $key, $index, $column) {
                                return Constants::getArticleStatus($model->status);
                            },
                            'filter' => Constants::getArticleStatus(),
                        ],
                        [
                            'attribute' => 'created_at',
                            'format' => ['date'],
                            'filter' => \yii\helpers\Html::activeInput('text', $searchModel, 'create_start_at', ['class'=>'form-control layer-date', 'placeholder'=>'', 'onclick'=>"laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'});"]).\yii\helpers\Html::activeInput('text', $searchModel, 'create_end_at', ['class'=>'form-control layer-date', 'placeholder'=>'', 'onclick'=>"laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})"]),
                        ],
                        [
                            'attribute' => 'updated_at',
                            'format' => ['date'],
                            'filter' => \yii\helpers\Html::activeInput('text', $searchModel, 'update_start_at', ['class'=>'form-control layer-date', 'placeholder'=>'', 'onclick'=>"laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})"]).\yii\helpers\Html::activeInput('text', $searchModel, 'update_end_at', ['class'=>'form-control layer-date', 'placeholder'=>'', 'onclick'=>"laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})"]),
                        ],
                        [
                            'class' => 'feehi\grid\ActionColumn',
                            'buttons' => [
                                'comment' => function($url, $model, $key){
                                    return Html::a('<i class="fa  fa-commenting-o" aria-hidden="true"></i> '. Yii::t('app', 'Comments'), Url::to(['comment/index', 'CommentSearch[aid]'=>$model->id]), [
                                        'title' => Yii::t('app', 'Comments'),
                                        'data-pjax' => '0',
                                        'class' => 'btn btn-white btn-sm J_menuItem',
                                    ]);
                                }
                            ],
                            'width' => '135',
                            'template' => '{view-layer} {update} {delete}{comment}',
                        ],
                    ]
                ]);
                ?>
                <?php Pjax::end();?>
            </div>
        </div>
    </div>
</div>
<?php $this->registerJs("
$(document).ready(function(){
    var t;
    $('table tr td a.title').hover(function(){
        t = setTimeout(function(){}, 200);
        var node = $(this).attr('title');
        if(node.length == 0){
            layer.tips('文章没有配图', $(this));
        }else {
            layer.tips('<img src='+node+'>', $(this));
        }
    },function(){
       clearTimeout(t);
    });
});"
)
?>