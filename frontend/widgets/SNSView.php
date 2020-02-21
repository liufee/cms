<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-02-20 10:41
 */

namespace frontend\widgets;


use Yii;
use feehi\components\Feehi;
use yii\helpers\Url;

class SNSView extends \yii\base\Widget
{

        public $template = '<div class="textwidget">
            <div class="social">
                <a href="{%WEIBO%}" rel="external nofollow" title="" target="_blank" data-original-title="新浪微博"><i class="sinaweibo fa fa-weibo"></i></a>
                <a href="{%FACEBOOK%}" rel="external nofollow" title="" target="_blank" data-original-title="Facebook"><i class="facebook fa fa-facebook"></i></a>
                <a class="weixin" data-original-title="" title=""><i class="weixins fa fa-weixin"></i>
                    <div class="weixin-popover">
                        <div class="popover bottom in">
                            <div class="arrow"></div>
                            <div class="popover-title">{%FOLLOW_WECHAT%}{%WECHAT%}</div>
                            <div class="popover-content"><img src="{%WECHAT_IMG%}"></div>
                        </div>
                    </div>
                </a>
                <a href="mailto:{%EMAIL%}" rel="external nofollow" title="" target="_blank" data-original-title="Email"><i class="email fa fa-envelope-o"></i></a>
                <a href="http://wpa.qq.com/msgrd?v=3&amp;uin={%QQ%}&amp;site=qq&amp;menu=yes" rel="external nofollow" title="" target="_blank" data-original-title="联系QQ"><i class="qq fa fa-qq"></i></a>
                <a href="{%RSS%}" rel="external nofollow" target="_blank" title="" data-original-title="订阅本站"><i class="rss fa fa-rss"></i></a>
            </div>
        </div>';

    public function run()
    {
        /** @var Feehi $feehi */
        $feehi = Yii::$app->get("feehi");
        $wechatImg = Yii::$app->getRequest()->getBaseUrl() . "/static/images/weixin.jpg";
        $template = str_replace("{%WEIBO%}", $feehi->weibo, $this->template);
        $template = str_replace("{%FACEBOOK%}", $feehi->facebook, $template);
        $template = str_replace("{%WECHAT%}", $feehi->wechat, $template);
        $template = str_replace("{%WECHAT_IMG%}", $wechatImg, $template);
        $template = str_replace("{%EMAIL%}", $feehi->email, $template);
        $template = str_replace("{%FOLLOW_WECHAT%}", Yii::t("frontend", "Follow Wechat"), $template);
        $template = str_replace("{%QQ%}", $feehi->qq, $template);
        $template = str_replace("{%RSS%}", Url::to(['article/rss']), $template);
        return $template;
    }
}