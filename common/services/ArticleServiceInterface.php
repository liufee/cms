<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-01-30 14:40
 */

namespace common\services;


use common\models\ArticleContent;

interface ArticleServiceInterface extends ServiceInterface
{
    const ServiceName = 'articleService';

    const ScenarioArticle = "article";

    const ScenarioPage = "page";


    public function newArticleContentModel(array $options= []);

    public function getArticleContentDetail($id, array $options = []);

    public function getFlagHeadLinesArticles($limit, $sort = SORT_DESC);

    public function getArticleSubTitle($subTitle);

    public function getArticleById($aid);
}