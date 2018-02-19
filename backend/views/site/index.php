<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

/* @var $this \yii\web\View */
/* @var $content string */

use common\helpers\FileDependencyHelper;
use yii\caching\FileDependency;
use yii\helpers\Html;
use backend\models\Menu;
use yii\helpers\Url;
use backend\assets\IndexAsset;

IndexAsset::register($this);
$this->title = yii::t('app', 'Backend Manage System');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="renderer" content="webkit">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <link rel="icon" href="<?= yii::$app->getRequest()->getHostInfo() ?>/favicon.ico" type="image/x-icon"/>
</head>
<body class="fixed-sidebar full-height-layout gray-bg" style="overflow:hidden">
<?php $this->beginBody() ?>
<div id="wrapper">
    <!--左侧导航开始-->
    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="nav-close"><i class="fa fa-times-circle"></i>
        </div>
        <div class="sidebar-collapse">
            <ul class="nav" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element">
                        <span>
                            <img alt="image" class="img-circle" width="64px" height="64px" src="<?php if (yii::$app->getUser()->getIdentity()->avatar) {echo yii::$app->params['site']['url'] . yii::$app->getUser()->getIdentity()->avatar;} else {echo 'static/img/profile_small.jpg';} ?>"/>
                        </span>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear">
                                <span class="block m-t-xs"><strong class="font-bold"><?= yii::t('menu', yii::$app->getUser()->getIdentity()->getRoleName())?></strong></span>
                                <span class="text-muted text-xs block"><?= yii::$app->getUser()->getIdentity()->username ?><b class="caret"></b></span>
                            </span>
                        </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li><a class="J_menuItem" href="<?= Url::to(['admin-user/update-self']) ?>"><?= yii::t('app', 'Profile') ?></a></li>
                            <li><a class="J_menuItem" href="<?= Url::to(['article/index']) ?>"><?= yii::t('app', 'Articles') ?></a></li>
                            <li><a target="_blank" href="<?= yii::$app->params['site']['url'] ?>"><?= yii::t('app', 'Frontend') ?></a></li>
                            <li class="divider"></li>
                            <li><a data-method="post" href="<?= Url::toRoute('site/logout') ?>"><?= yii::t('app', 'Logout') ?></a></li>
                        </ul>
                    </div>
                    <div class="logo-element">H+</div>
                </li>
                <?php
                $cacheDependencyObject = yii::createObject([
                    'class' => FileDependencyHelper::className(),
                    'fileName' => 'backend_menu.txt',
                ]);
                $dependency = [
                    'class' => FileDependency::className(),
                    'fileName' => $cacheDependencyObject->createFile(),
                ];
                if ($this->beginCache('backend_menu', [
                    'variations' => [
                        Yii::$app->language,
                        yii::$app->getUser()->getId()
                    ],
                    'dependency' => $dependency
                ])
                ) {
                    ?>
                    <?= Menu::getBackendMenu(); ?>
                    <?php
                    $this->endCache();
                }
                ?>
            </ul>
        </div>
    </nav>
    <!--左侧导航结束-->
    <!--右侧部分开始-->
    <div id="page-wrapper" class="gray-bg dashbard-1">
        <div class="row border-bottom">
            <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header" style="width: 50%;">
                    <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                </div>
                <ul class="nav navbar-top-links navbar-right">
                    <li class="hidden-xs">
                        <a href="<?= yii::$app->params['site']['url'] ?>" target='_blank'><i class="fa fa-internet-explorer"></i> <?= yii::t('app', 'Frontend') ?></a>
                    </li>
                    <li class="hidden-xs">
                        <a href="javascript:void(0)" onclick="reloadIframe()"><i class="fa fa-refresh"></i> <?= yii::t('app', 'Refresh') ?></a>
                    </li>
                    <li class="hidden-xs">
                        <a href="http://cms.feehi.com/help" class="J_menuItem" data-index="0"><i class="fa fa-cart-arrow-down"></i> <?= yii::t('app', 'Support') ?></a>
                    </li>
                    <li class="dropdown hidden-xs">
                        <a class="right-sidebar-toggle" aria-expanded="false"><i class="fa fa-tasks"></i> <?= yii::t('app', 'Theme') ?></a>
                    </li>
                    <li class="hidden-xs">
                        <select onchange="location.href=this.options[this.selectedIndex].value;">
                            <option
                                <?php if (yii::$app->language == 'zh-CN') {
                                echo 'selected';
                                } ?> value="<?= Url::to(['site/language', 'lang' => 'zh-CN']) ?>">简体中文
                            </option>
                            <option
                                <?php if (yii::$app->language == 'en-US') {
                                    echo "selected";
                                } ?> value="<?= Url::to(['site/language', 'lang' => 'en-US']) ?>">English
                            </option>
                        </select>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="row content-tabs">
            <button class="roll-nav roll-left J_tabLeft"><i class="fa fa-backward"></i>
            </button>
            <nav class="page-tabs J_menuTabs">
                <div class="page-tabs-content">
                    <a href="javascript:;" class="active J_menuTab" data-id="<?= Url::to(['site/main']) ?>"><?= yii::t('app', 'Home') ?></a>
                </div>
            </nav>
            <button class="roll-nav roll-right J_tabRight"><i class="fa fa-forward"></i></button>
            <div class="btn-group roll-nav roll-right">
                <button class="dropdown J_tabClose" data-toggle="dropdown"><?= yii::t('app', 'Close') ?><span class="caret"></span></button>
                <ul role="menu" class="dropdown-menu dropdown-menu-right">
                    <li class="J_tabShowActive"><a><?= yii::t('app', 'Locate Current Tab') ?></a></li>
                    <li class="divider"></li>
                    <li class="J_tabCloseAll"><a><?= yii::t('app', 'Close All Tab') ?></a></li>
                    <li class="J_tabCloseOther"><a><?= yii::t('app', 'Close Other Tab') ?></a></li>
                </ul>
            </div>
            <?= Html::a('<i class="fa fa fa-sign-out"></i>' . yii::t('app', 'Logout'), Url::toRoute('site/logout'), ['data-method'=>'post', 'class'=>'roll-nav roll-right J_tabExit'])?>
        </div>
        <div class="row J_mainContent" id="content-main">
            <iframe class="J_iframe" name="iframe0" width="100%" height="100%" src="<?= Url::to(['site/main']) ?>" frameborder="0" data-id="<?= Url::to(['site/main']) ?>" seamless></iframe>
        </div>
        <div class="footer">
            <div class="pull-right">&copy; 2015-<?=date('Y')?> <a href="http://blog.feehi.com/" target="_blank">feehi</a></div>
        </div>
    </div>
    <!--右侧部分结束-->
    <!--右侧边栏开始-->
    <div id="right-sidebar">
        <div class="sidebar-container">
            <ul class="nav nav-tabs navs-3">
                <li class="active">
                    <a data-toggle="tab" href="#tab-1">
                        <i class="fa fa-gear"></i> <?=yii::t('app', 'Theme')?>
                    </a>
                </li><!--
                <li class=""><a data-toggle="tab" href="#tab-2">
                        通知
                    </a>
                </li>
                <li><a data-toggle="tab" href="#tab-3">
                        项目进度
                    </a>
                </li>-->
            </ul>

            <div class="tab-content">
                <div id="tab-1" class="tab-pane active">
                    <div class="sidebar-title">
                        <h3><i class="fa fa-comments-o"></i> 主题设置</h3>
                        <small><i class="fa fa-tim"></i> 你可以从这里选择和预览主题的布局和样式，这些设置会被保存在本地，下次打开的时候会直接应用这些设置。</small>
                    </div>
                    <div class="skin-setttings">
                        <div class="title">主题设置</div>
                        <div class="setings-item">
                            <span>收起左侧菜单</span>
                            <div class="switch">
                                <div class="onoffswitch">
                                    <input type="checkbox" name="collapsemenu" class="onoffswitch-checkbox"
                                           id="collapsemenu">
                                    <label class="onoffswitch-label" for="collapsemenu">
                                        <span class="onoffswitch-inner"></span>
                                        <span class="onoffswitch-switch"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="setings-item">
                            <span>固定顶部</span>

                            <div class="switch">
                                <div class="onoffswitch">
                                    <input type="checkbox" name="fixednavbar" class="onoffswitch-checkbox"
                                           id="fixednavbar">
                                    <label class="onoffswitch-label" for="fixednavbar">
                                        <span class="onoffswitch-inner"></span>
                                        <span class="onoffswitch-switch"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="setings-item">
                                <span>
                        固定宽度
                    </span>

                            <div class="switch">
                                <div class="onoffswitch">
                                    <input type="checkbox" name="boxedlayout" class="onoffswitch-checkbox"
                                           id="boxedlayout">
                                    <label class="onoffswitch-label" for="boxedlayout">
                                        <span class="onoffswitch-inner"></span>
                                        <span class="onoffswitch-switch"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="title">皮肤选择</div>
                        <div class="setings-item default-skin nb">
                                <span class="skin-name ">
                         <a href="#" class="s-skin-0">
                             默认皮肤
                         </a>
                    </span>
                        </div>
                        <div class="setings-item blue-skin nb">
                                <span class="skin-name ">
                        <a href="#" class="s-skin-1">
                            蓝色主题
                        </a>
                    </span>
                        </div>
                        <div class="setings-item yellow-skin nb">
                                <span class="skin-name ">
                        <a href="#" class="s-skin-3">
                            黄色/紫色主题
                        </a>
                    </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->endBody() ?>
</body>
<script>
    function reloadIframe() {
        var current_iframe = $("iframe:visible");
        current_iframe[0].contentWindow.location.reload();
        return false;
    }
    if (window.top !== window.self) {
        window.top.location = window.location;
    }
</script>
</html>
<?php $this->endPage() ?>
