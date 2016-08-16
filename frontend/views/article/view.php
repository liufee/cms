<?php
/**
 * Created by PhpStorm.
 * User: lf
 * Date: 16/4/2
 * Time: 22:55
 */
use yii\helpers\Url;
use frontend\assets\ViewAsset;
use feehi\widgets\JsBlock;
use yii\helpers\Html;

$this->title = $model->title;
$this->registerMetaTag(['keywords' => $model->seo_keywords]);
$this->registerMetaTag(['description' => $model->seo_description]);
$this->registerMetaTag(['tags' => $model->tag]);
$this->registerMetaTag(['property'=>'article:author', 'content'=>$model->author_name]);
$categoryName = $model->category ? $model->category->name : '未分类';
ViewAsset::register($this);
?>
<div class="content-wrap">
    <div class="content">
        <div class="breadcrumbs">
            <a title="返回首页" href="<?=yii::$app->homeUrl?>"><i class="fa fa-home"></i></a> <small>&gt;</small>
            <a href="<?= Url::to(['article/index', 'cat'=>$categoryName]) ?>"><?=$categoryName ?></a> <small>&gt;</small> <span class="muted"><?=$model->title?></span>
        </div>
        <header class="article-header">
            <h1 class="article-title"><a href="<?=Url::to(['article/view', 'id'=>$model->id])?>"><?=$model->title?></a></h1>
            <div class="meta">
                <span id="mute-category" class="muted"><i class="fa fa-list-alt"></i><a href="<?= Url::to(['article/index', 'cat'=>$categoryName]) ?>"> <?= $categoryName ?></a></span><span class="muted"><i class="fa fa-user"></i> <a href="">admin</a></span>
                <time class="muted"><i class="fa fa-clock-o"></i> <?=yii::$app->formatter->asDate($model->created_at)?></time>
                <span class="muted"><i class="fa fa-eye"></i> <?=$model->scan_count * 100?>℃</span>
                <span class="muted"><i class="fa fa-comments-o"></i> <a href="<?=Url::to(['article/view', 'id'=>$model->id])?>#comments"><?=$model->comment_count?>评论</a></span>
            </div>
        </header>

        <article class="article-content">
            <?= $model->articleContent->content?>
            <p>转载请注明：<a href="<?=yii::$app->homeUrl?>" data-original-title="" title=""><?=yii::$app->feehi->website_title?></a> » <a href="<?=Url::to(['article/view', 'id'=>$model->id])?>" data-original-title="" title=""><?=$model->title?></a></p>

            <div class="article-social">
                <div class="bdsharebuttonbox"><a href="#" class="bds_more" data-cmd="more"></a><a href="#" class="bds_qzone" data-cmd="qzone" title="分享到QQ空间"></a><a href="#" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a><a href="#" class="bds_tqq" data-cmd="tqq" title="分享到腾讯微博"></a><a href="#" class="bds_renren" data-cmd="renren" title="分享到人人网"></a><a href="#" class="bds_weixin" data-cmd="weixin" title="分享到微信"></a></div>
                <!--<script>window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"","bdMini":"1","bdMiniList":false,"bdPic":"","bdStyle":"1","bdSize":"16"},"share":{},"image":{"viewList":["qzone","tsina","tqq","renren","weixin"],"viewText":"分享到：","viewSize":"16"},"selectShare":{"bdContainerClass":null,"bdSelectMiniList":["qzone","tsina","tqq","renren","weixin"]}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];</script>
                <!--
                <a href="javascript:;" data-action="ding" data-id="4" id="Addlike" class="action" data-original-title="" title="">
                   <i class="fa fa-heart-o"></i>喜欢 (<span class="count">4</span>)
                </a>
                <span class="or">or</span>
                <span class="action action-share bdsharebuttonbox bdshare-button-style0-24" data-bd-bind="1466409001285"><i class="fa fa-share-alt"></i>分享 (<span class="bds_count" data-cmd="count" title="累计分享0次">0</span>)
                    <div class="action-popover">
                        <div class="popover top in">
                            <div class="arrow"></div>
                            <div class="popover-content">
                                <a href="http://demo7.ledkongzhiqi.com/zhuti/4.html#" class="sinaweibo fa fa-weibo" data-cmd="tsina" title="" data-original-title="分享到新浪微博"></a>
                                <a href="http://demo7.ledkongzhiqi.com/zhuti/4.html#" class="bds_qzone fa fa-star" data-cmd="qzone" title="" data-original-title="分享到QQ空间"></a>
                                <a href="http://demo7.ledkongzhiqi.com/zhuti/4.html#" class="tencentweibo fa fa-tencent-weibo" data-cmd="tqq" title="" data-original-title="分享到腾讯微博"></a>
                                <a href="http://demo7.ledkongzhiqi.com/zhuti/4.html#" class="qq fa fa-qq" data-cmd="sqq" title="" data-original-title="分享到QQ好友"></a>
                                <a href="http://demo7.ledkongzhiqi.com/zhuti/4.html#" class="bds_renren fa fa-renren" data-cmd="renren" title="" data-original-title="分享到人人网"></a>
                                <a href="http://demo7.ledkongzhiqi.com/zhuti/4.html#" class="bds_weixin fa fa-weixin" data-cmd="weixin" title="" data-original-title="分享到微信"></a>
                                <a href="http://demo7.ledkongzhiqi.com/zhuti/4.html#" class="bds_more fa fa-ellipsis-h" data-cmd="more" data-original-title="" title=""></a>
                            </div>
                        </div>
                    </div>
                </span>
                -->
            </div>
        </article>
        <footer class="article-footer"></footer>
        <nav class="article-nav">
            <?php
            if($prev !== NULL){
            ?>
            <span class="article-nav-prev">
                <i class="fa fa-angle-double-left"></i>
                <a href='<?=Url::to(['article/view', 'id'=>$prev->id])?>' rel="prev"><?=$prev->title?></a>
            </span>
            <?php }?>
            <?php
            if($next != NULL){
            ?>
            <span class="article-nav-next">
                <a href="<?=Url::to(['article/view', 'id'=>$next->id])?>" rel="next"><?=$next->title?></a>
                <i class="fa fa-angle-double-right"></i>
            </span>
            <?php }?>
        </nav>

        <div class="related_top">
            <div class="related_posts">
                <ul class="related_img">
                    <h2>相关推荐</h2>
                    <?php
                    //$articles = Article::getArticleLists(['flag_picture'=>1], 8, 'rand()');
                    $articles = $recommends;
                    foreach ($articles as $article){
                        $url = Url::to(['article/view', 'id'=>$article->id]);
                        echo
                        "<li class='related_box'>
                             <a href='{$url}' title='{$article->title}' target='_blank'>
                                <img src='/timthumb.php?w=185&h=110&zc=0&src={$article->thumb}' alt='{$article->title}'><br>
                                <span class='r_title'>{$article->title}</span>
                             </a>
                        </li>";
                    }
                    ?>
                </ul>
            </div>
        </div>

        <div id="respond" class="no_webshot">
            <form action="" method="post" id="commentform">
            <?php $form = \yii\bootstrap\ActiveForm::begin();?>
                <?=Html::activeHiddenInput($commentModel, 'aid', ['value'=>$model->id])?>
                <div class="comt-title" style="display: block;">
                    <div class="comt-avatar pull-left">
                        <img src="https://secure.gravatar.com/avatar/" class="avatar avatar-108" height="50" width="50">
                    </div>
                    <div class="comt-author pull-left"><?php if(yii::$app->user->isGuest){echo '游客';}else{echo yii::$app->user->identity->username;} ?> <span>发表我的评论</span> &nbsp;
                        <a class="switch-author" href="javascript:void(0)" data-type="switch-author" style="font-size:12px;">换个身份</a>
                    </div>
                    <a id="cancel-comment-reply-link" class="pull-right" href="javascript:;">取消评论</a>
                </div>

                <div class="comt">
                    <div class="comt-box">
                        <?=$form->field($commentModel, 'content', ['template'=>'{input}'])->textarea(['class'=>'input-block-level comt-area', 'cols'=>'100%', 'rows'=>'3', 'tabindex'=>1, 'placeholder'=>"写点什么...", "id"=>"comment"]) ?>
                        <div class="comt-ctrl">
                            <button class="btn btn-primary pull-right" type="submit" name="submit" id="submit" tabindex="5"><i class="fa fa-check-square-o"></i> 提交评论</button>
                            <div class="comt-tips pull-right">
                                <div class="comt-tip comt-error" style="display: none;"></div>
                                <input type='hidden' name='comment_post_ID' value='114' id='comment_post_ID' />
                                <?= $form->field($commentModel, 'reply_to', ['template'=>'{input}'])->hiddenInput(['value'=>0, 'id'=>'comment_parent']) ?>
                                <p style="display: none;"><input type="hidden" id="akismet_comment_nonce" name="akismet_comment_nonce" value="32920dc775" /></p><p style="display: none;"><input type="hidden" id="ak_js" name="ak_js" value="101"/></p>
                            </div>
                            <span data-type="comment-insert-smilie" class="muted comt-smilie"><i class="fa fa-smile-o"></i> 表情</span>
                                <span class="muted comt-mailme"><label for="comment_mail_notify" class="checkbox inline" style="padding-top:0">
                                    <input type="checkbox" name="comment_mail_notify" id="comment_mail_notify" value="comment_mail_notify" checked="checked">有人回复时邮件通知我</label>
                                </span>
                        </div>
                    </div>
                    <div class="comt-comterinfo" id="comment-author-info" style="display:none">
                        <h4>Hi，您需要填写昵称和邮箱！</h4>
                        <ul>
                            <li class="form-inline">
                                <label class="hide" for="author">昵称</label>
                                    <?php if(yii::$app->user->isGuest){$defaultNickname = '游客';}else{$defaultNickname = yii::$app->user->identity->username;} ?>
                                    <?= $form->field($commentModel, 'nickname', ['template'=>'{input}<span class="help-inline">昵称 (必填)</span>'])->textInput(['value'=>$defaultNickname]) ?>
                            </li>
                            <li class="form-inline"><?= $form->field($commentModel, 'email', ['template'=>'{input}<span class="help-inline">邮箱 </span>'])->textInput() ?></li>
                            <li class="form-inline"><?= $form->field($commentModel, 'website_url', ['template'=>'{input}<span class="help-inline">网址</span>'])->textInput() ?></li></li>
                        </ul>
                    </div>
                </div>
            <?php \yii\bootstrap\ActiveForm::end() ?>
        </div>
        <div id="postcomments">
            <div id="comments">
                <i class="fa fa-comments-o"></i> <b> (<?=$model->comment_count?>)</b>个小伙伴在吐槽
            </div>
            <ol class="commentlist">
                <?php
                    foreach ($commentList as $v){
                ?>
                <li class="comment even thread-even depth-1 byuser comment-author-admin bypostauthor" id="comment-<?=$v['id']?>">
                    <div class="c-avatar">
                        <img data-original="https://secure.gravatar.com/avatar/<?=md5($v['email'])?>?s=50" class="avatar avatar-108" height="50" width="50" src="https://secure.gravatar.com/avatar/<?= md5($v['nickname'])?>" style="display: block;">
                        <div class="c-main" id="div-comment-<?=$v['id']?>">
                            <?=$v['content']?><br>
                            <div class="c-meta">
                                <span class="c-author"><a href="<?=$v['website_url']?>" rel="external nofollow" class="url" target="_blank"><?= empty($v['nickname']) ? '游客' : $v['nickname'] ?></a></span><?=yii::$app->formatter->asDate($v['created_at'])?> (<?= \feehi\libs\Help::tranTime($v['created_at']) ?>)
                                <a rel="nofollow" class="comment-reply-link" href="" onclick="return addComment.moveForm('div-comment-<?=$v['id']?>', '<?=$v['id']?>', 'respond','0' )" aria-label="回复给admin">回复</a>
                            </div>
                        </div>
                    </div>
                    <?php
                    if(!empty($v['sub'])){
                    ?>
                    <ul class="children">
                        <?php
                            foreach ($v['sub'] as $value){
                        ?>
                        <li class="comment odd alt depth-2" id="comment-<?=$value['id']?>">
                            <div class="c-avatar">
                                <img data-original="https://secure.gravatar.com/avatar/<?=md5($v['email'])?>?s=50" class="avatar avatar-108" height="50" width="50" src="https://secure.gravatar.com/avatar/<?= md5($v['nickname'])?>" style="display: block;">
                                <div class="c-main" id="div-comment-<?=$value['id']?>"><?=$value['content']?><br>
                                    <div class="c-meta"><span class="c-author"><a href="<?=$v['website_url']?>" rel="external nofollow" class="url" target="_blank"><?= empty($value['nickname']) ? '游客' : $value['nickname'] ?></a></span><?=yii::$app->formatter->asDate($value['created_at'])?>  (<?= \feehi\libs\Help::tranTime($value['created_at']) ?>)</div>
                                </div>
                            </div>
                        </li><!-- #comment-## -->
                        <?php } ?>
                    </ul>
                    <?php } ?>
                </li><!-- #comment-## -->
                <?php } ?>
            </ol>
            <div class="commentnav">
            </div>
        </div>
    </div>
</div>
<?=$this->render('/widgets/_sidebar')?>
<?php JsBlock::begin(); ?>
<script type="text/javascript">
    SyntaxHighlighter.all();
</script>
<?php JsBlock::end(); ?>
