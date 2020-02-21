<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-01-30 14:40
 */

namespace common\services;


use backend\models\search\ArticleSearch;
use common\libs\Constants;
use common\models\Article;

class ArticleService extends Service implements ArticleServiceInterface
{

    public function getSearchModel(array $query, array $options = [])
    {
        return new ArticleSearch();
    }

    public function getModel($id, array $options = [])
    {
        $model = Article::findOne($id);
        if( isset( $options['scenario']) ){
            $model->setScenario( $options['scenario'] );
        }
        return $model;
    }

    public function getNewModel(array $options = [])
    {
        $type = Article::ARTICLE;
        isset($options['scenario']) && $type = $options['scenario'];
        $model = new Article(['scenario' => $type]);
        $model->loadDefaultValues();
        return $model;
    }

    public function getFlagHeadLinesArticles($limit, $sort = SORT_DESC)
    {
        return Article::find()->limit($limit)->where(['flag_headline'=>Constants::YesNo_Yes])->limit($limit)->with('category')->orderBy(["sort"=>$sort])->all();
    }

    public function getArticleSubTitle($subTitle)
    {
        return Article::findOne(['type' => Article::SINGLE_PAGE, 'sub_title' => $subTitle]);
    }

    public function getArticleById($aid)
    {
        return Article::find()->where(['id'=>$aid, "status"=>Constants::YesNo_Yes, 'type'=>Article::ARTICLE])->one();
    }
}