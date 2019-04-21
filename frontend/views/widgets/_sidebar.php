<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-06-21 14:26
 */

use common\models\meta\ArticleMetaTag;
use common\models\Options;
use frontend\models\Article;
use yii\helpers\Url;
use frontend\models\Comment;
use frontend\models\FriendlyLink;

?>
<aside class="sidebar">
    <div class="widget widget_text">
        <div class="textwidget">
            <div class="social">
                <a href="<?= Yii::$app->feehi->weibo ?>" rel="external nofollow" title="" target="_blank" data-original-title="新浪微博"><i class="sinaweibo fa fa-weibo"></i></a>
                <a href="<?= Yii::$app->feehi->facebook ?>" rel="external nofollow" title="" target="_blank" data-original-title="Facebook"><i class="facebook fa fa-facebook"></i></a>
                <a class="weixin" data-original-title="" title=""><i class="weixins fa fa-weixin"></i>
                    <div class="weixin-popover">
                        <div class="popover bottom in">
                            <div class="arrow"></div>
                            <div class="popover-title"><?=Yii::t('frontend', 'Follow Wechat')?>“<?= Yii::$app->feehi->wechat ?>”</div>
                            <div class="popover-content"><img src="<?=Yii::$app->getRequest()->getBaseUrl()?>/static/images/weixin.jpg"></div>
                        </div>
                    </div>
                </a>
                <a href="mailto:<?= Yii::$app->feehi->email ?>" rel="external nofollow" title="" target="_blank" data-original-title="Email"><i class="email fa fa-envelope-o"></i></a>
                <a href="http://wpa.qq.com/msgrd?v=3&amp;uin=<?= Yii::$app->feehi->qq ?>&amp;site=qq&amp;menu=yes" rel="external nofollow" title="" target="_blank" data-original-title="联系QQ"><i class="qq fa fa-qq"></i></a>
                <a href="<?= Url::to(['article/rss'])?>" rel="external nofollow" target="_blank" title="" data-original-title="订阅本站"><i class="rss fa fa-rss"></i></a>
            </div>
        </div>
    </div>
    <div class="widget d_textbanner">
        <a class="style03" target="_blank"
           href="http://shang.qq.com/wpa/qunwpa?idkey=3693ea25b07705069bc9210c5272830f2b00bd891b14bb6f60ce7bb070570aa9">
            <strong><?=Yii::t('frontend', 'Join group')?></strong>
            <h2><?=Yii::t('frontend', 'Official QQ group - main')?></h2>
            <p><?=Yii::t('frontend', 'FeehiCMS official QQ group number: {number}', ['number'=>'258780872'])?>
                <br>
                <br>
                <img border="0" src="<?=Yii::$app->getRequest()->getBaseUrl()?>/static/images/group.png" alt="feehi cms" title="feehi cms">
            </p>
        </a>
    </div>

    <div class="widget d_textbanner">
        <a class="style01" target="_blank" href="http://cms.feehi.com">
            <strong><?=Yii::t('frontend', 'New generation CMS FeehiCMS')?></strong>
            <h2><?=Yii::t('frontend', 'Highly recommend')?></h2>
            <p><?=Yii::t('frontend', 'FeehiCMS based on yii2, support php7, makes website more excellent...')?></p>
        </a>
    </div>

    <div class="widget d_banner">
        <div class="d_banner_inner">
            <?php $ad = Options::getAdByName('sidebar_right_1')?>
            <a href="<?=$ad->link?>" target="<?=$ad->target?>" title="<?=$ad->desc?>"  rel="external nofollow">
                <img src="<?=$ad->ad?>" alt="<?=$ad->desc?>"><span></span>
            </a>
        </div>
    </div>

    <div class="widget d_postlist">
        <div class="title">
            <h2>
                <sapn class="title_span"><?= Yii::t('frontend', 'Hot Recommends') ?></sapn>
            </h2>
        </div>
        <ul>
            <?php
            $articles = Article::find()->where(['flag_special_recommend' => 1])->limit(8)->orderBy("sort asc")->all();
            foreach ($articles as $article) {
                /** @var $article \frontend\models\Article */
                $url = Url::to(['article/view', 'id' => $article->id]);
                $imgUrl = $article->getThumbUrlBySize(125, 86);
                $article->created_at = Yii::$app->formatter->asDate($article->created_at);
                echo "<li>
                    <a href=\"{$url}\" title=\"{$article->title}\">
                        <span class=\"thumbnail\"><img src=\"{$imgUrl}\" alt=\"{$article->title}\"></span>
                        <span class=\"text\">{$article->title}</span>
                        <span class=\"muted\">{$article->created_at}</span><span class=\"muted_1\">{$article->comment_count}" . Yii::t('frontend', ' Comments') . "</span>
                    </a>
                </li>";
            }
            ?>
        </ul>
    </div>

    <div class="widget d_banner">
        <div class="d_banner_inner">
            <img class="alignnone size-full wp-image-516" src="<?php $ad = Options::getAdByName('sidebar_right_2');echo $ad->ad?>" alt="ddy" width="308">
        </div>
    </div>
    <div class="widget d_tag">
        <div class="title">
            <h2>
                <sapn class="title_span"><?= Yii::t('frontend', 'Clound Tags') ?></sapn>
            </h2>
        </div>
        <div class="d_tags">
            <?php
            $tagsModel = new ArticleMetaTag();
            foreach ($tagsModel->getHotestTags() as $k => $v) {
                echo "<a title='' href='" . Url::to(['search/tag', 'tag' => $k]) . "' data-original-title='{$v}" . Yii::t('frontend', ' Topics') . "'>{$k} ({$v})</a>";
            }
            ?>
        </div>
    </div>

    <div class="widget d_comment">
        <div class="title">
            <h2>
                <sapn class="title_span"><?= Yii::t('frontend', 'Latest Comments') ?></sapn>
            </h2>
        </div>
        <ul>
            <?php
            $comments = Comment::find()->orderBy("id desc")->limit(5)->all();
            foreach ($comments as $v) {
                ?>
                <li>
                    <a href="<?= Url::to(['article/view', 'id' => $v['aid'], '#' => 'comment-' . $v['id']]) ?>"
                       title="">
                        <img data-original="<?=Yii::$app->getRequest()->getBaseUrl()?>/static/images/comment-user-avatar.png" class="avatar avatar-72" height="50"
                             width="50" src="" style="display: block;">
                        <div class="muted">
                            <i><?= $v['nickname'] ?></i>&nbsp;&nbsp;<?= Yii::$app->formatter->asRelativeTime($v['created_at']) ?>
                            (<?= Yii::$app->formatter->asTime($v['created_at']) ?>)<?= Yii::t('frontend', ' said') ?>
                            ：<br><?= $v['content'] ?></div>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
    <div class="widget widget_text">
        <div class="title">
            <h2>
                <sapn class="title_span"><?= Yii::t('frontend', 'Friendly Links') ?></sapn>
            </h2>
        </div>
        <div class="textwidget">
            <div class="d_tags_1">
                <?php
                $links = FriendlyLink::find()->where(['status' => FriendlyLink::DISPLAY_YES])->orderBy("sort asc, id asc")->asArray()->all();
                foreach ($links as $link) {
                    echo "<a target='_blank' href='{$link['url']}'>{$link['name']}</a>";
                }
                ?>
            </div>
        </div>
    </div>
</aside>
