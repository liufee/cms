<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-06-21 14:26
 */

/**
 * @var $rightAd1 \backend\models\form\AdForm
 * @var $rightAd2 \backend\models\form\AdForm
 */

use frontend\widgets\FriendlyLinkView;
use frontend\widgets\HottestArticleTagView;
use frontend\widgets\LatestCommentView;
use frontend\widgets\RecentCommentArticleView;
use frontend\widgets\SNSView;

?>
<aside class="sidebar">
    <div class="widget widget_text">
        <div class="textwidget">
            <?=SNSView::widget()?>
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
            <a href="<?=$rightAd1->link?>" target="<?=$rightAd1->target?>" title="<?=$rightAd1->desc?>"  rel="external nofollow">
                <img src="<?=$rightAd1->ad?>" alt="<?=$rightAd1->desc?>"><span></span>
            </a>
        </div>
    </div>

    <div class="widget d_postlist">
        <div class="title">
            <h2>
                <sapn class="title_span"><?= Yii::t('frontend', 'Hot Recommends') ?></sapn>
            </h2>
        </div>
        <?=RecentCommentArticleView::widget()?>
    </div>

    <div class="widget d_banner">
        <div class="d_banner_inner">
            <a href="<?=$rightAd2->link?>" target="<?=$rightAd2->target?>" title="<?=$rightAd2->desc?>"  rel="external nofollow">
                <img class="alignnone size-full wp-image-516" src="<?= $rightAd2->ad?>" alt="ddy" width="308">
            </a>
        </div>
    </div>
    <div class="widget d_tag">
        <div class="title">
            <h2>
                <sapn class="title_span"><?= Yii::t('frontend', 'Clound Tags') ?></sapn>
            </h2>
        </div>
        <div class="d_tags">
            <?=HottestArticleTagView::widget()?>
        </div>
    </div>

    <div class="widget d_comment">
        <div class="title">
            <h2>
                <sapn class="title_span"><?= Yii::t('frontend', 'Latest Comments') ?></sapn>
            </h2>
        </div>
       <?=LatestCommentView::widget()?>
    </div>
    <div class="widget widget_text">
        <div class="title">
            <h2>
                <sapn class="title_span"><?= Yii::t('frontend', 'Friendly Links') ?></sapn>
            </h2>
        </div>
        <div class="textwidget">
            <div class="d_tags_1">
                <?=FriendlyLinkView::widget()?>
            </div>
        </div>
    </div>
</aside>
