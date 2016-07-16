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
use backend\models\Article;

$this->title = 'Articles';

?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget()?>
                <?php Pjax::begin(['id'=>'pjax']);?>
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
                                return $model->category->name;
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
                            'attribute' => 'thumb',
                            'filter' => Constants::getYesNoItems(),
                            'value' => function($model, $key, $index, $column) {
                                if($model->thumb == '')
                                    $num = 0;
                                else
                                    $num = 1;
                                return Constants::getYesNoItems($num);
                            },
                        ],
                        [
                            'attribute' => 'flag_headline',
                            'filter' => Constants::getYesNoItems(),
                            'format' => 'html',
                            'value' => function($model, $key, $index, $column) {
                                $text = Constants::getYesNoItems($model->flag_headline);
                                if($model->flag_headline){
                                    $url = Url::to(['change-status', 'id'=>$model->id, 'status'=>0, 'field'=>'flag_headline']);
                                    return Html::a($text, $url, ['class'=>'btn btn-info btn-xs btn-rounded']);
                                }else{
                                    $url = Url::to(['change-status', 'id'=>$model->id, 'status'=>1, 'field'=>'flag_headline']);
                                    return Html::a($text, $url, ['class'=>'btn btn-default btn-xs btn-rounded']);
                                }
                            },
                        ],
                        [
                            'attribute' => 'flag_recommend',
                            'filter' => Constants::getYesNoItems(),
                            'format' => 'html',
                            'value' => function($model, $key, $index, $column) {
                                $text = Constants::getYesNoItems($model->flag_recommend);
                                if($model->flag_recommend){
                                    $url = Url::to(['change-status', 'id'=>$model->id, 'status'=>0, 'field'=>'flag_recommend']);
                                    return Html::a($text, $url, ['class'=>'btn btn-info btn-xs btn-rounded']);
                                }else{
                                    $url = Url::to(['change-status', 'id'=>$model->id, 'status'=>1, 'field'=>'flag_recommend']);
                                    return Html::a($text, $url, ['class'=>'btn btn-default btn-xs btn-rounded']);
                                }
                            },
                        ],
                        [
                            'attribute' => 'flag_slide_show',
                            'filter' => Constants::getYesNoItems(),
                            'format' => 'html',
                            'value' => function($model, $key, $index, $column) {
                                $text = Constants::getYesNoItems($model->flag_slide_show);
                                if($model->flag_slide_show){
                                    $url = Url::to(['change-status', 'id'=>$model->id, 'status'=>0, 'field'=>'flag_slide_show']);
                                    return Html::a($text, $url, ['class'=>'btn btn-info btn-xs btn-rounded']);
                                }else{
                                    $url = Url::to(['change-status', 'id'=>$model->id, 'status'=>1, 'field'=>'flag_slide_show']);
                                    return Html::a($text, $url, ['class'=>'btn btn-default btn-xs btn-rounded']);
                                }
                            },
                        ],
                        [
                            'attribute' => 'flag_special_recommend',
                            'filter' => Constants::getYesNoItems(),
                            'format' => 'html',
                            'value' => function($model, $key, $index, $column) {
                                $text = Constants::getYesNoItems($model->flag_special_recommend);
                                if($model->flag_special_recommend){
                                    $url = Url::to(['change-status', 'id'=>$model->id, 'status'=>0, 'field'=>'flag_special_recommend']);
                                    return Html::a($text, $url, ['class'=>'btn btn-info btn-xs btn-rounded']);
                                }else{
                                    $url = Url::to(['change-status', 'id'=>$model->id, 'status'=>1, 'field'=>'flag_special_recommend']);
                                    return Html::a($text, $url, ['class'=>'btn btn-default btn-xs btn-rounded']);
                                }
                            },
                        ],
                        [
                            'attribute' => 'flag_roll',
                            'filter' => Constants::getYesNoItems(),
                            'format' => 'html',
                            'value' => function($model, $key, $index, $column) {
                                $text = Constants::getYesNoItems($model->flag_roll);
                                if($model->flag_roll){
                                    $url = Url::to(['change-status', 'id'=>$model->id, 'status'=>0, 'field'=>'flag_roll']);
                                    return Html::a($text, $url, ['class'=>'btn btn-info btn-xs btn-rounded']);
                                }else{
                                    $url = Url::to(['change-status', 'id'=>$model->id, 'status'=>1, 'field'=>'flag_roll']);
                                    return Html::a($text, $url, ['class'=>'btn btn-default btn-xs btn-rounded']);
                                }
                            },
                        ],
                        [
                            'attribute' => 'flag_bold',
                            'filter' => Constants::getYesNoItems(),
                            'format' => 'html',
                            'value' => function($model, $key, $index, $column) {
                                $text = Constants::getYesNoItems($model->flag_bold);
                                if($model->flag_bold){
                                    $url = Url::to(['change-status', 'id'=>$model->id, 'status'=>0, 'field'=>'flag_bold']);
                                    return Html::a($text, $url, ['class'=>'btn btn-info btn-xs btn-rounded']);
                                }else{
                                    $url = Url::to(['change-status', 'id'=>$model->id, 'status'=>1, 'field'=>'flag_bold']);
                                    return Html::a($text, $url, ['class'=>'btn btn-default btn-xs btn-rounded']);
                                }
                            },
                        ],
                        [
                            'attribute' => 'flag_picture',
                            'filter' => Constants::getYesNoItems(),
                            'format' => 'html',
                            'value' => function($model, $key, $index, $column) {
                                $text = Constants::getYesNoItems($model->flag_picture);
                                if($model->flag_picture){
                                    $url = Url::to(['change-status', 'id'=>$model->id, 'status'=>0, 'field'=>'flag_picture']);
                                    return Html::a($text, $url, ['class'=>'btn btn-info btn-xs btn-rounded']);
                                }else{
                                    $url = Url::to(['change-status', 'id'=>$model->id, 'status'=>1, 'field'=>'flag_picture']);
                                    return Html::a($text, $url, ['class'=>'btn btn-default btn-xs btn-rounded']);
                                }
                            },
                        ],
                        [
                            'attribute' => 'status',
                            'format' => 'html',
                            'value' => function($model, $key, $index, $column) {
                                $text = Constants::getArticleStatus($model->status);
                                if($model->status == Article::ARTICLE_PUBLISHED){
                                    $url = Url::to(['change-status', 'id'=>$model->id, 'status'=>Article::ARTICLE_DRAFT]);
                                    return "<a href='$url' class='btn btn-info btn-xs btn-rounded'>{$text}</a>";
                                }else{
                                    $url = Url::to(['change-status', 'id'=>$model->id, 'status'=>Article::ARTICLE_PUBLISHED]);
                                    return "<a href='$url' class='btn  btn-xs btn-default btn-rounded'>{$text}</a>";
                                }
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
<script>
    function showImg(){
        t = setTimeout(function(){}, 200);
        var node = $(this).attr('title');
        if(node.length == 0){
            layer.tips('<?=yii::t('app', 'No picture')?>', $(this));
        }else {
            layer.tips('<img src='+node+'>', $(this));
        }
    }
</script>
<?php $this->registerJs("
$(document).ready(function(){
    var t;
    $('table tr td a.title').hover(showImg,function(){
       clearTimeout(t);
    });
});
var container = $('#pjax');
container.on('pjax:send',function(args){
    layer.load(2);
});
container.on('pjax:complete',function(args){
    layer.closeAll('loading');
    $('table tr td a.title').bind('mouseover mouseout', showImg);
});
"
)
?>