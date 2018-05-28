<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-02 22:55
 */

/**
 * @var $this yii\web\View
 * @var $model frontend\models\Article
 * @var $commentModel frontend\models\Comment
 * @var $prev frontend\models\Article
 * @var $next frontend\models\Article
 * @var $recommends array
 * @var $commentList array
 */

use frontend\widgets\ArticleListView;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use frontend\assets\ViewAsset;
use common\widgets\JsBlock;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = $model->title;

$this->registerMetaTag(['name' => 'keywords', 'content' => $model->seo_keywords], 'keywords');
$this->registerMetaTag(['name' => 'description', 'content' => $model->seo_description], 'description');
$this->registerMetaTag(['name' => 'tags', 'content' => call_user_func(function()use($model) {
    $tags = '';
    foreach ($model->articleTags as $tag) {
        $tags .= $tag->value . ',';
    }
    return rtrim($tags, ',');
    }
)], 'tags');
$this->registerMetaTag(['property' => 'article:author', 'content' => $model->author_name]);
$categoryName = $model->category ? $model->category->name : Yii::t('app', 'uncategoried');

ViewAsset::register($this);
?>
<div class="content-wrap">
    <div class="content">
        <div class="breadcrumbs">
            <a title="<?=Yii::t('frontend', 'Return Home')?>" href="<?= Yii::$app->getHomeUrl() ?>"><i class="fa fa-home"></i></a>
            <small>&gt;</small>
            <a href="<?= Url::to(['article/index', 'cat' => $categoryName]) ?>"><?= $categoryName ?></a>
            <small>&gt;</small>
            <span class="muted"><?= $model->title ?></span>
        </div>
        <header class="article-header">
            <h1 class="article-title"><a href="<?= Url::to(['article/view', 'id' => $model->id]) ?>"><?= $model->title ?></a></h1>
            <div class="meta">
                <span id="mute-category" class="muted"><i class="fa fa-list-alt"></i>
                    <a href="<?= Url::to([
                        'article/index',
                        'cat' => $categoryName
                    ]) ?>"> <?= $categoryName ?>
                    </a>
                </span>
                <span class="muted"><i class="fa fa-user"></i> <a href="">admin</a></span>
                <time class="muted"><i class="fa fa-clock-o"></i> <?= Yii::$app->getFormatter()->asDate($model->created_at) ?></time>
                <span class="muted"><i class="fa fa-eye"></i> <span id="scanCount"><?= $model->scan_count * 100 ?></span>℃</span>
                <span class="muted"><i class="fa fa-comments-o"></i>
                    <a href="<?= Url::to([
                        'article/view',
                        'id' => $model->id
                    ]) ?>#comments">
                        <span id="commentCount"><?= $model->comment_count ?></span>
                    <?=Yii::t('frontend', 'Comment')?></a>
                </span>
            </div>
        </header>

        <article class="article-content">
            <?= $model->articleContent->content ?>
            <p>
                <?= Yii::t('frontend', 'Reproduced please indicate the source') ?>：
                <a href="<?= Yii::$app->homeUrl ?>" data-original-title="" title=""><?= Yii::$app->feehi->website_title ?></a>
                »
                <a href="<?= Url::to(['article/view', 'id' => $model->id]) ?>" data-original-title="" title=""><?= $model->title ?></a>
            </p>

            <div class="article-social">
                <a href="javascript:;" data-action="ding" data-id="<?=$model->id?>" like-url="<?=Url::to(['article/like'])?>" id="Addlike" class="action"><i class="fa fa-heart-o"></i><?=Yii::t('frontend', 'Like')?> (<span class="count"><?= $model->getArticleLikeCount() ?></span>)</a>
                <span class="or">or</span>
                <span class="action action-share bdsharebuttonbox"><i class="fa fa-share-alt"></i><?=Yii::t('frontend', 'Share')?> (<span class="bds_count" data-cmd="count" title="累计分享0次">0</span>)
                    <div class="action-popover">
                        <div class="popover top in"><div class="arrow"></div>
                            <div class="popover-content">
                                <a href="#" class="sinaweibo fa fa-weibo" data-cmd="tsina" title="分享到新浪微博"></a>
                                <a href="#" class="bds_qzone fa fa-star" data-cmd="qzone" title="分享到QQ空间"></a>
                                <a href="#" class="tencentweibo fa fa-tencent-weibo" data-cmd="tqq" title="分享到腾讯微博"></a>
                                <a href="#" class="qq fa fa-qq" data-cmd="sqq" title="分享到QQ好友"></a>
                                <a href="#" class="bds_renren fa fa-renren" data-cmd="renren" title="分享到人人网"></a>
                                <a href="#" class="bds_weixin fa fa-weixin" data-cmd="weixin" title="分享到微信"></a>
                                <a href="#" class="bds_more fa fa-ellipsis-h" data-cmd="more"></a>
                            </div>
                        </div>
                    </div>
                </span>
            </div>
        </article>
        <footer class="article-footer">
            <div class="article-tags">
                <i class="fa fa-tags"></i>
                <?php foreach ($model->articleTags as $tag){ ?>
                    <a href="<?=Url::to(['search/tag', 'tag'=>$tag->value])?>" rel="tag" data-original-title="" title=""><?=$tag->value?></a>
                <?php } ?>
            </div>
        </footer>
        <nav class="article-nav">
            <?php
                if ($prev !== null) {
            ?>
                <span class="article-nav-prev">
                    <i class="fa fa-angle-double-left"></i><a href='<?= Url::to(['article/view', 'id' => $prev->id]) ?>' rel="prev"><?= $prev->title ?></a>
                </span>
            <?php } ?>
            <?php
                if ($next != null) {
            ?>
                <span class="article-nav-next">
                    <a href="<?= Url::to(['article/view', 'id' => $next->id]) ?>" rel="next"><?= $next->title ?></a><i class="fa fa-angle-double-right"></i>
                </span>
            <?php } ?>
        </nav>

        <div class="related_top">
            <div class="related_posts">
                <?= ArticleListView::widget([
                    'dataProvider' => new ArrayDataProvider([
                        'allModels' => $recommends,
                    ]),
                    'layout' => "<ul class='related_img'><h2>" . Yii::t('frontend', 'Related Recommends') . "</h2>{items}</ul>",
                    'template' => "<a href='{article_url}' title='{title}' target='_blank'>
                                        <img src='{img_url}' alt='{title}'><br>
                                        <span class='r_title'>{title}</span>
                                     </a>",
                    'itemOptions' => ['tag'=>'li', 'class'=>'related_box'],
                    'thumbWidth' => 185,
                    'thumbHeight' => 110,
                ]) ?>
            </div>
        </div>

        <div id="respond" class="no_webshot">
            <form action="" method="post" id="commentform">
                <?php $form = ActiveForm::begin(); ?>
                <?= Html::activeHiddenInput($commentModel, 'aid', ['value' => $model->id]) ?>
                <div class="comt-title" style="display: block;">
                    <div class="comt-avatar pull-left">
                        <img src="https://secure.gravatar.com/avatar/" class="avatar avatar-108" height="50" width="50">
                    </div>
                    <div class="comt-author pull-left"><?php if (Yii::$app->getUser()->getIsGuest()) {
                            echo Yii::t('frontend', 'Guest');
                        } else {
                            echo Yii::$app->getUser()->getIdentity()->username;
                        } ?> <span><?= Yii::t('frontend', 'Post my comment') ?></span> &nbsp;
                        <a class="switch-author" href="javascript:void(0)" data-type="switch-author" style="font-size:12px;"><?= Yii::t('frontend', 'Change account') ?></a>
                    </div>
                    <a id="cancel-comment-reply-link" class="pull-right" href="javascript:;"><?= Yii::t('frontend', 'Cancel comment') ?></a>
                </div>

                <div class="comt">
                    <div class="comt-box">
                        <?= $form->field($commentModel, 'content', ['template' => '{input}'])->textarea([
                            'class' => 'input-block-level comt-area',
                            'cols' => '100%',
                            'rows' => '3',
                            'tabindex' => 1,
                            'placeholder' => Yii::t('frontend', 'Writing some...'),
                            "id" => "comment"
                        ]) ?>
                        <div class="comt-ctrl">
                            <button class="btn btn-primary pull-right" type="submit" name="submit" id="submit" tabindex="5">
                                <i class="fa fa-check-square-o"></i> <?= Yii::t('frontend', 'Submit comment') ?>
                            </button>
                            <div class="comt-tips pull-right">
                                <div class="comt-tip comt-error" style="display: none;"></div>
                                <input type='hidden' name='comment_post_ID' value='114' id='comment_post_ID'/>
                                <?= $form->field($commentModel, 'reply_to', ['template' => '{input}'])->hiddenInput(['value' => 0, 'id' => 'comment_parent']) ?>
                                <p style="display: none;"><input type="hidden" id="akismet_comment_nonce" name="akismet_comment_nonce" value="32920dc775"/></p>
                                <p style="display: none;"><input type="hidden" id="ak_js" name="ak_js" value="101"/></p>
                            </div>
                            <span data-type="comment-insert-smilie" class="muted comt-smilie"><i class="fa fa-smile-o"></i> <?= Yii::t('frontend', 'emoj') ?></span>
                            <span class="muted comt-mailme"><label for="comment_mail_notify" class="checkbox inline" style="padding-top:0">
                                <input type="checkbox" name="comment_mail_notify" id="comment_mail_notify" value="comment_mail_notify" checked="checked"><?=Yii::t('frontend', 'Send email at someone replied')?></label>
                            </span>
                        </div>
                    </div>
                    <div class="comt-comterinfo" id="comment-author-info" style="display:none">
                        <h4><?= Yii::t('frontend', 'Hi, Please fill') ?></h4>
                        <ul>
                            <li class="form-inline">
                                <label class="hide" for="author"><?= Yii::t('app', 'Nickname') ?></label>
                                <?php if (Yii::$app->getUser()->getIsGuest()) {
                                    $defaultNickname = Yii::t('frontend', 'Guest');
                                } else {
                                    $defaultNickname = Yii::$app->getUser()->getIdentity()->username;
                                } ?>
                                <?= $form->field($commentModel, 'nickname', ['template' => '{input}<span class="help-inline">' . Yii::t('app', 'Nickname') . ' (' . Yii::t('frontend', 'required') . ')</span>'])->textInput(['value' => $defaultNickname]) ?>
                            </li>
                            <li class="form-inline"><?= $form->field($commentModel, 'email', ['template' => '{input}<span class="help-inline">' . Yii::t('app', 'Email') . ' </span>'])->textInput() ?></li>
                            <li class="form-inline"><?= $form->field($commentModel, 'website_url', ['template' => '{input}<span class="help-inline">' . Yii::t('frontend', 'Website') . '</span>'])->textInput() ?></li>
                        </ul>
                    </div>
                </div>
                <?php ActiveForm::end() ?>
        </div>
        <div id="postcomments">
            <div id="comments">
                <i class="fa fa-comments-o"></i> <b> (<?= $model->comment_count ?>)</b><?= Yii::t('frontend', 'person posted') ?>
            </div>
            <ol class="commentlist">
                <?php
                foreach ($commentList as $v) {
                    ?>
                    <li class="comment even thread-even depth-1 byuser comment-author-admin bypostauthor"
                        id="comment-<?= $v['id'] ?>">
                        <div class="c-avatar">
                            <img data-original="https://secure.gravatar.com/avatar/<?= md5($v['email']) ?>?s=50" class="avatar avatar-108" height="50" width="50" src="https://secure.gravatar.com/avatar/<?= md5($v['nickname']) ?>" style="display: block;">
                            <div class="c-main" id="div-comment-<?= $v['id'] ?>">
                                <?= $v['content'] ?><br>
                                <div class="c-meta">
                                    <span class="c-author"><a href="<?= $v['website_url'] ?>" rel="external nofollow" class="url" target="_blank"><?= empty($v['nickname']) ? '游客' : $v['nickname'] ?></a></span><?= Yii::$app->formatter->asDate($v['created_at']) ?>
                                    (<?= Yii::$app->getFormatter()->asRelativeTime($v['created_at']) ?>)
                                    <a rel="nofollow" class="comment-reply-link" href="" onclick="return addComment.moveForm('div-comment-<?= $v['id'] ?>', '<?= $v['id'] ?>', 'respond','0' )" aria-label="回复给admin">回复</a>
                                </div>
                            </div>
                        </div>
                        <?php
                        if (! empty($v['sub'])) {
                            ?>
                            <ul class="children">
                                <?php
                                    foreach ($v['sub'] as $value) {
                                ?>
                                    <li class="comment odd alt depth-2" id="comment-<?= $value['id'] ?>">
                                        <div class="c-avatar">
                                            <img data-original="https://secure.gravatar.com/avatar/<?= md5($v['email']) ?>?s=50" class="avatar avatar-108" height="50" width="50" src="https://secure.gravatar.com/avatar/<?= md5($v['nickname']) ?>" style="display: block;">
                                            <div class="c-main" id="div-comment-<?= $value['id'] ?>"><?= $value['content'] ?><br>
                                                <div class="c-meta">
                                                    <span class="c-author">
                                                        <a href="<?= $v['website_url'] ?>" rel="external nofollow" class="url" target="_blank"><?= empty($value['nickname']) ? Yii::t('frontend', "Guest") : $value['nickname'] ?></a>
                                                    </span>
                                                    <?= Yii::$app->getFormatter()->asDate($value['created_at']) ?>(<?= Yii::$app->getFormatter()->asRelativeTime($value['created_at']) ?>)
                                                </div>
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
<?= $this->render('/widgets/_sidebar') ?>
<?php JsBlock::begin(); ?>
<script type="text/javascript">
    SyntaxHighlighter.all();
    $(document).ready(function () {
        $.ajax({
            url:"<?=Url::to(['article/view-ajax'])?>",
            data:{id:<?=$model->id?>},
            success:function (data) {
                $("span.count").html(data.likeCount);
                $("span#scanCount").html(data.scanCount);
                $("span#commentCount").html(data.commentCount);
            }
        });
    })
</script>
<script>with(document)0[(getElementsByTagName("head")[0]||body).appendChild(createElement("script")).src="http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion="+~(-new Date()/36e5)];</script>
<?php JsBlock::end(); ?>
