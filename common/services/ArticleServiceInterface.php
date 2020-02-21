<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-01-30 14:40
 */

namespace common\services;


interface ArticleServiceInterface extends ServiceInterface
{
    const ServiceName = 'articleService';

    public function getFlagHeadLinesArticles($limit, $sort = SORT_DESC);

    public function getArticleSubTitle($subTitle);

    public function getArticleById($aid);
}