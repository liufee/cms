<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-06-21 14:26
 */

use frontend\controllers\components\Article;
use yii\helpers\Url;
use frontend\models\Comment;
use frontend\models\FriendLink;

?>
<aside class="sidebar">
    <div class="widget widget_text">
        <div class="textwidget">
            <div class="social">
                <a href="<?= yii::$app->feehi->weibo ?>" rel="external nofollow" title="" target="_blank"
                   data-original-title="新浪微博"><i class="sinaweibo fa fa-weibo"></i></a>
                <a href="<?= yii::$app->feehi->facebook ?>" rel="external nofollow" title="" target="_blank"
                   data-original-title="Facebook"><i class="facebook fa fa-facebook"></i></a>
                <a class="weixin" data-original-title="" title=""><i class="weixins fa fa-weixin"></i>
                    <div class="weixin-popover">
                        <div class="popover bottom in">
                            <div class="arrow"></div>
                            <div class="popover-title">订阅号“<?= yii::$app->feehi->wechat ?>”</div>
                            <div class="popover-content"><img src="/static/images/weixin.jpg"></div>
                        </div>
                    </div>
                </a>
                <a href="mailto:<?= yii::$app->feehi->email ?>" rel="external nofollow" title="" target="_blank"
                   data-original-title="Email"><i class="email fa fa-envelope-o"></i></a>
                <a href="http://wpa.qq.com/msgrd?v=3&amp;uin=<?= yii::$app->feehi->qq ?>&amp;site=qq&amp;menu=yes"
                   rel="external nofollow" title="" target="_blank" data-original-title="联系QQ"><i
                            class="qq fa fa-qq"></i></a>
                <a href="<?= yii::$app->feehi->rss ?>" rel="external nofollow" target="_blank" title=""
                   data-original-title="订阅本站"><i class="rss fa fa-rss"></i></a>
            </div>
        </div>
    </div>
    <div class="widget d_textbanner">
        <a class="style03" target="_blank"
           href="http://shang.qq.com/wpa/qunwpa?idkey=3693ea25b07705069bc9210c5272830f2b00bd891b14bb6f60ce7bb070570aa9">
            <strong>加群啦</strong>
            <h2>官方QQ群-主群</h2>
            <p>飞嗨官方QQ群-主群 群号：258780872，欢迎大家！
                <br>
                <br>
                <img border="0" src="/static/images/group.png" alt="feehi cms" title="feehi cms">
            </p>
        </a>
    </div>

    <div class="widget d_textbanner">
        <a class="style01" href="http://cms.feehi.com">
            <strong>Feehi cms 新一代内容管理系统</strong>
            <h2>吐血推荐</h2>
            <p>Feehi cms是一款机遇优秀php框架yii2开发的新一代cms系统，使用了php最新版本(php7)带来的新特性，会让网站显得内涵而出色...</p>
        </a>
    </div>

    <div class="widget d_banner">
        <div class="d_banner_inner">
            <a href="http://www.feehi.com" target="_blank" title="feehi cms">
                <img src="/static/images/cms.jpg" alt="feehi cms"><span></span>
            </a>
        </div>
    </div>

    <div class="widget d_postlist">
        <div class="title">
            <h2>
                <sapn class="title_span"><?= yii::t('frontend', 'Hot Recommends') ?></sapn>
            </h2>
        </div>
        <ul>
            <?php
            $articles = Article::getArticleLists(['flag_roll' => 1], 8);
            foreach ($articles as $article) {
                $url = Url::to(['article/view', 'id' => $article->id]);
                $article->created_at = yii::$app->formatter->asDate($article->created_at);
                echo "<li>
                    <a href=\"{$url}\" title=\"{$article->title}\">
                        <span class=\"thumbnail\"><img src=\"/timthumb.php?w=125&h=86&zc=0&src={$article->thumb}\" alt=\"{$article->title}\"></span>
                        <span class=\"text\">{$article->title}</span>
                        <span class=\"muted\">{$article->created_at}</span><span class=\"muted_1\">{$article->comment_count}" . yii::t('frontend', ' Comments') . "</span>
                    </a>
                </li>";
            }
            ?>
        </ul>
    </div>

    <div class="widget d_banner">
        <div class="d_banner_inner">
            <img class="alignnone size-full wp-image-516" src="/static/images/t01605ab9200e1b43f8.jpg" alt="ddy"
                 width="308">
        </div>
    </div>
    <div class="widget d_tag">
        <div class="title">
            <h2>
                <sapn class="title_span"><?= yii::t('frontend', 'Clound Tags') ?></sapn>
            </h2>
        </div>
        <div class="d_tags">
            <?php
            $tags = Article::getTags(12);
            foreach ($tags as $k => $v) {
                echo "<a title='' href='" . Url::to([
                        'search/index',
                        'q' => $k
                    ]) . "' data-original-title='{$v}" . yii::t('frontend', ' Topics') . "'>{$k} ({$v})</a>";
            }
            ?>
        </div>
    </div>

    <div class="widget d_subscribe">
        <div class="title">
            <h2>
                <sapn class="title_span"><?= yii::t('frontend', 'Email Suscribe') ?></sapn>
            </h2>
        </div>
        <form action="http://list.qq.com/cgi-bin/qf_compose_send" target="_blank" method="post">
            <p><?= yii::t('frontend', 'Subscribe to the wonderful content') ?></p>
            <input type="hidden" name="t" value="qf_booked_feedback">
            <input type="hidden" name="id" value="">
            <input type="email" name="to" class="rsstxt" placeholder="your@email.com" value="" required="">
            <input type="submit" class="rssbutton" value="<?= yii::t('frontend', 'Subscribe') ?>">
        </form>
    </div>
    <div class="widget d_comment">
        <div class="title">
            <h2>
                <sapn class="title_span"><?= yii::t('frontend', 'Latest Comments') ?></sapn>
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
                        <img data-original="/static/images/comment-user-avatar.png" class="avatar avatar-72" height="50"
                             width="50" src="" style="display: block;">
                        <div class="muted">
                            <i><?= $v['nickname'] ?></i>&nbsp;&nbsp;<?= yii::$app->formatter->asRelativeTime($v['created_at']) ?>
                            (<?= yii::$app->formatter->asTime($v['created_at']) ?>)<?= yii::t('frontend', ' said') ?>
                            ：<br><?= $v['content'] ?></div>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
    <div class="widget widget_text">
        <div class="title">
            <h2>
                <sapn class="title_span"><?= yii::t('frontend', 'Frinendly Links') ?></sapn>
            </h2>
        </div>
        <div class="textwidget">
            <div class="d_tags_1">
                <?php
                $links = FriendLink::find()->where(['status' => FriendLink::DISPLAY_YES])->asArray()->all();
                foreach ($links as $v) {
                    echo "<a target='_blank' href='{$v['url']}'>{$v['name']}</a>";
                }
                ?>
            </div>
        </div>
    </div>
</aside>
