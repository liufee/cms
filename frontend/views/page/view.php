<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-06-21 11:07
 */

/**
 * @var $this yii\web\View
 * @var $model frontend\models\Article
 */

use frontend\models\Article;
use yii\helpers\Url;

$this->title = $model->title . '-' . Yii::$app->feehi->website_title;

$this->registerMetaTag(['name' => 'keywords', 'content' => $model->seo_keywords], 'keywords');
$this->registerMetaTag(['name' => 'description', 'content' => $model->seo_description], 'description');
$this->registerMetaTag(['name' => 'tags', 'content'=> call_user_func(function()use($model) {
    $tags = '';
    foreach ($model->articleTags as $tag) {
        $tags .= $tag->value . ',';
    }
    return rtrim($tags, ',');
}
)], 'tags');
?>
<div class="pagewrapper clearfix">
    <aside class="pagesidebar">
        <ul class="pagesider-menu">
            <?php
            $menus = Article::find()->where(['type' => Article::SINGLE_PAGE])->all();
            foreach ($menus as $menu) {
                $url = Url::to(['page/view', 'name'=>$menu['sub_title']]);
                $current = '';
                if (Yii::$app->request->get('id', '') == $menu->id) {
                    $current = " current-menu-item current-page-item ";
                }
                echo "<li class='menu-item menu-item-type-post_type menu-item-object-page {$current} page_item page-item-{$menu->id} menu-item-{$menu->id}'><a href='{$url}'>{$menu->title}</a></li>";
            }
            ?>
        </ul>
    </aside>
    <div class="pagecontent">
        <header class="pageheader clearfix">
            <h1 class="pull-left">
                <?= $model->title ?>
            </h1>
            <div class="pull-right">
            </div>
        </header>
        <div class="article-content">
            <?= $model->articleContent->content ?>
        </div>
    </div>
</div>
