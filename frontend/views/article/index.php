<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

use yii\helpers\Url;
use frontend\widgets\ArticleListView;
use frontend\controllers\components\Article;
use frontend\widgets\ScrollPicView;
use common\widgets\JsBlock;
use frontend\assets\IndexAsset;

IndexAsset::register($this);
$this->title = yii::$app->feehi->website_title;
?>
<div class="content-wrap">
    <div class="content">
        <div class="slick_bor">
            <?= ScrollPicView::widget([
                'dataProvider' => Article::getArticleList(['flag_slide_show' => 1]),
            ]) ?>
            <div class="ws_shadow"></div>
        </div>
        <div class="daodu clr">
            <div class="tip"><h4><?= yii::t('frontend', 'Well-choosen') ?></h4></div>
            <ul class="dd-list">
                <?php
                $articles = Article::getArticleLists(['flag_special_recommend' => 1], 4);
                foreach ($articles as $article) {
                    $url = Url::to(['article/view', 'id' => $article->id]);
                    $article->created_at = yii::$app->formatter->asDate($article->created_at);
                    $article->summary = yii\helpers\StringHelper::truncate($article->summary, 20);
                    echo "<li>
                        <figure class='dd-img'>
                            <a title='{$article->title}' target='_blank' href='{$url}'>
                                <img src='/timthumb.php?w=168&h=112&zc=0&src={$article->thumb}' style='display: inline;' alt='{$article->title}'>
                            </a>
                        </figure>
                        <div class='dd-content'>
                            <h2 class='dd-title'>
                                <a rel='bookmark' title='{$article->title}' href='{$url}'>{$article->title}</a>
                            </h2>
                            <div class='dd-site xs-hidden'>{$article->summary}</div>
                        </div>
                    </li>";
                }
                ?>
            </ul>
        </div>

        <header class="archive-header"><h1><?= yii::t('frontend', 'Latest Articles') ?></h1></header>
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
