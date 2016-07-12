<?php
/**
 * Created by PhpStorm.
 * User: f
 * Date: 16/6/21
 * Time: 上午11:07
 */
$this->registerMetaTag(['keywords' => $model->seo_keywords]);
$this->registerMetaTag(['description' => $model->seo_description]);
$this->registerMetaTag(['tags' => $model->tag]);
?>
<div class="pagewrapper clearfix">
    <aside class="pagesidebar">
        <ul class="pagesider-menu">
			<?php
			    $menus = \frontend\models\Article::find()->where(['type'=>\frontend\models\Article::SINGLE_PAGE])->all();
			    foreach ($menus as $menu){
					$url =  '/'.$menu['sub_title'];
					$current = '';
					if(yii::$app->request->get('id', '') == $menu->id){
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
                <a href="<?=\yii\helpers\Url::to(['page/view', 'id'=>$model->id])?>"><?=$model->title?></a>
            </h1>
			<div class="pull-right"><!-- 百度分享 -->
				<span class="action action-share bdsharebuttonbox bdshare-button-style0-24" data-bd-bind="1466394492236"><i class="fa fa-share-alt"></i>分享 (<span class="bds_count" data-cmd="count" title="累计分享3次">3</span>)<div class="action-popover"><div class="popover top in"><div class="arrow"></div><div class="popover-content"><a href="http://demo7.ledkongzhiqi.com/cuizl#" class="sinaweibo fa fa-weibo" data-cmd="tsina" title="分享到新浪微博"></a><a href="http://demo7.ledkongzhiqi.com/cuizl#" class="bds_qzone fa fa-star" data-cmd="qzone" title="分享到QQ空间"></a><a href="http://demo7.ledkongzhiqi.com/cuizl#" class="tencentweibo fa fa-tencent-weibo" data-cmd="tqq" title="分享到腾讯微博"></a><a href="http://demo7.ledkongzhiqi.com/cuizl#" class="qq fa fa-qq" data-cmd="sqq" title="分享到QQ好友"></a><a href="http://demo7.ledkongzhiqi.com/cuizl#" class="bds_renren fa fa-renren" data-cmd="renren" title="分享到人人网"></a><a href="http://demo7.ledkongzhiqi.com/cuizl#" class="bds_weixin fa fa-weixin" data-cmd="weixin" title="分享到微信"></a><a href="http://demo7.ledkongzhiqi.com/cuizl#" class="bds_more fa fa-ellipsis-h" data-cmd="more"></a></div></div></div></span>			</div>
		</header>
		<div class="article-content">
            <?=$model->content?>
		</div>
	</div>
</div>
