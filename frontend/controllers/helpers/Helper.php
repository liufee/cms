<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-02-21 14:06
 */

namespace frontend\controllers\helpers;

use common\services\AdServiceInterface;
use common\services\ArticleServiceInterface;
use common\services\BannerServiceInterface;
use Yii;

class Helper
{
    public static function getCommonInfos()
    {
        /** @var ArticleServiceInterface $articleService */
        $articleService = Yii::$app->get(ArticleServiceInterface::ServiceName);
        /** @var BannerServiceInterface $bannerService */
        $bannerService = Yii::$app->get(BannerServiceInterface::ServiceName);
        /** @var AdServiceInterface $adService */
        $adService = Yii::$app->get(AdServiceInterface::ServiceName);

        $headLineArticles = $articleService->getFlagHeadLinesArticles(4);
        $indexBanners = $bannerService->getBannersByAdType("index");
        $rightAd1 = $adService->getAdByName("sidebar_right_1");
        $rightAd2 = $adService->getAdByName("sidebar_right_2");
        return [
            'headLinesArticles' => $headLineArticles,
            "indexBanners" => $indexBanners,
            "rightAd1" => $rightAd1,
            "rightAd2" => $rightAd2,
        ];
    }
}