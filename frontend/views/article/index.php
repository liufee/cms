<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $type string
 */

use common\models\Options;
use frontend\models\Article;
use frontend\widgets\ArticleListView;
use frontend\widgets\ScrollPicView;
use common\widgets\JsBlock;
use frontend\assets\IndexAsset;
use yii\data\ArrayDataProvider;

IndexAsset::register($this);
$this->title = Yii::$app->feehi->website_title;
?>
<div class="content-wrap">
    <div class="content">
        <div class="slick_bor">
            <?= ScrollPicView::widget([
                'banners' => Options::getBannersByType('index'),
            ]) ?>
            <div class="ws_shadow"></div>
        </div>
        <div class="daodu clr">
            <?= ArticleListView::widget([
                'dataProvider' => new ArrayDataProvider([
                    'allModels' => Article::find()->limit(1)->where(['flag_headline'=>1])->limit(4)->with('category')->orderBy("sort asc")->all(),
                ]),
                'layout' => "<div class='tip'><h4>" . Yii::t('frontend', 'Well-choosen') . "</h4></div>
                                <ul class=\"dd-list\">
                                    {items}
                                </ul>
                             ",
                'template' => "<figure class='dd-img'>
                                        <a title='{title}' target='_blank' href='{article_url}'>
                                            <img src='{img_url}' style='display: inline;' alt='{title}'>
                                        </a>
                                    </figure>
                                    <div class='dd-content'>
                                        <h2 class='dd-title'>
                                            <a rel='bookmark' title='{title}' href='{article_url}'>{title}</a>
                                        </h2>
                                        <div class='dd-site xs-hidden'>{summary}</div>
                                    </div>",
                'itemOptions' => ['tag'=>'li'],
                'thumbWidth' => 168,
                'thumbHeight' => 112,
            ]) ?>
        </div>

        <header class="archive-header"><h1><?=$type?></h1></header>
        <?= ArticleListView::widget([
            'dataProvider' => $dataProvider,
        ]) ?>
    </div>
</div>
<?= $this->render('/widgets/_sidebar') ?>
<?php JsBlock::begin() ?>
<script>
    $(function () {
        var mx = document.body.clientWidth;
        $(".slick").responsiveSlides({
            auto: true,
            pager: true,
            nav: true,
            speed: 700,
            timeout: 7000,
            maxwidth: mx,
            namespace: "centered-btns"
        });
    });
</script>
<?php JsBlock::end() ?>
