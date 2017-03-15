<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-06-21 11:07
 */

$this->registerMetaTag(['keywords' => $model->seo_keywords]);
$this->registerMetaTag(['description' => $model->seo_description]);
$this->registerMetaTag(['tags' => $model->tag]);
?>
<div class="pagewrapper clearfix">
    <aside class="pagesidebar">
        <ul class="pagesider-menu">
            <?php
            $menus = \frontend\models\Article::find()->where(['type' => \frontend\models\Article::SINGLE_PAGE])->all();
            foreach ($menus as $menu) {
                $url = '/' . $menu['sub_title'];
                $current = '';
                if (yii::$app->request->get('id', '') == $menu->id) {
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
