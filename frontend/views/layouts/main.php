<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use frontend\assets\AppAsset;
use yii\helpers\Url;
use frontend\widgets\MenuView;

AppAsset::register($this);
if(yii::$app->controller->action->id != 'view' && yii::$app->controller->id != 'page' ) {
    $this->registerMetaTag(['keywords' => yii::$app->feehi->seo_keywords]);
    $this->registerMetaTag(['description' => yii::$app->feehi->seo_description]);
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <meta charset="<?= yii::$app->charset ?>">
    <?= Html::csrfMetaTags() ?>
    <meta http-equiv="X-UA-Compatible" content="IE=10,IE=9,IE=8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <script>
        window._deel = {name: '<?=yii::$app->feehi->website_title?>',url: '<?=yii::$app->homeUrl?>', comment_url: '<?=Url::to(['article/comment'])?>', ajaxpager: '', commenton: 0, roll: [4,]}
    </script>
    <style>p.help-block{color:red}</style>
</head>
<?php $this->beginBody() ?>
<body class="home blog">
<header id="masthead" class="site-header">
    <nav id="top-header">
        <div class="top-nav">
            <div id="user-profile">
            <span class="nav-set">
                <span class="nav-login">
                    <?php
                    if(yii::$app->user->isGuest){
                    ?>
                    <a href="<?=Url::to(['site/login'])?>" class="signin-loader">Hi, 请登录</a>&nbsp; &nbsp;
                    <a href="<?=Url::to(['site/signup'])?>" class="signup-loader">我要注册</a>
                    <?php }else{ ?>
                        Welcome, <?=Html::encode( yii::$app->user->identity->username )?>
                        <a href="<?=Url::to(['site/logout'])?>" class="signup-loader">退出</a>
                    <?php }?>
              </span>
            </span>
            </div>
            <div class="menu-container">
                <ul id="menu-page" class="top-menu">
                    <a target="_blank" href="/about">关于我们</a> | <a target="_blank" href="/contact">联系我们</a>
                </ul>
            </div>
        </div>
    </nav>
    <div id="nav-header" class="">
        <div id="top-menu">
            <div id="top-menu_1">
                <span class="nav-search"><i class="fa fa-search"></i></span>
                <span class="nav-search_1"><i class="fa fa-navicon"></i></span>
                <hgroup class="logo-site" style="margin-top: 10px;">
                    <h1 class="site-title"> <a href="<?=yii::$app->homeUrl?>"><img src="/static/images/logo.png" alt="<?=yii::$app->feehi->website_title?>"></a></h1>
                </hgroup>
                <div id="site-nav-wrap">
                    <nav id="site-nav" class="main-nav">
                        <div>
                            <?= MenuView::widget()?>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <?= MenuView::widget([
        'template' => '<nav><ul class="nav_sj" id="nav-search_1">{lis}</ul></nav>',
        'liTemplate' => "<li class='menu-item menu-item-type-custom menu-item-object-custom current-menu-item current_page_item menu-item-home menu-item-{menu_id}'><a href='{url}'>{title}</a>{sub_menu}</li>"
    ])?>
</header>

<div id="search-main">
    <div id="searchbar">
        <form id="searchform" action="<?=Url::to('/search')?>" method="get">
            <input id="s" type="text" name="q" value="<?= Html::encode( yii::$app->request->get('q') ) ?>" required="" placeholder="输入搜索内容" name="s" value="">
            <button id="searchsubmit" type="submit">搜索</button>
        </form>
    </div>
    <div id="searchbar">
        <form id="searchform" target="_blank" action="https://www.baidu.com/s" method="get">
            <input type="hidden" name="entry" value="1">
            <input class="swap_value" name="w" placeholder="输入百度站内搜索关键词">
            <button id="searchsubmit" type="submit">百度</button>
        </form>
    </div>
    <div class="clear"></div>
</div>
<section class="container">
    <div class="speedbar"></div>
    <?= $content?>
</section>

<div class="branding branding-black">
    <div class="container_f">
        <h2>高效，专业，符合SEO</h2><a class="btn btn-lg" href="http://www.feehi.com" target="_blank">联系我们</a>
    </div>
</div>

<footer class="footer">
    <div class="footer-inner">
        <p>
            <a href="http://www.feehi.com/" title="Feehi CMS">Feehi CMS</a> 版权所有，保留一切权利   © 2010-2016&nbsp;&nbsp;
        </p>
        <p>Powered by Feehi CMS · Theme by <a title="飞嗨" href="http://blog.feehi.com">飞嗨</a> </p>
    </div>
</footer>
<div class="rollto" style="display: none;">
    <button class="btn btn-inverse" data-type="totop" title="回顶部"><i class="fa fa-arrow-up"></i></button>
</div>

</body>
<?php $this->endBody() ?>
<?php
    if(yii::$app->feehi->website_statics_script){
        echo yii::$app->feehi->website_statics_script;
    }
?>
</html>
<?php $this->endPage() ?>
