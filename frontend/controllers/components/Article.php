<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-04 16:53
 */

namespace frontend\controllers\components;

use common\models\meta\ArticleMetaTag;
use yii\base\Object;
use yii\data\ActiveDataProvider;
use frontend\models\Article as ArticleModel;

class Article extends Object
{

    /**
     * 根据点击量获取文章列表
     *
     * @param integer $limit 要取的文章数目
     * @param string $cid 分类id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getArticleByClick($limit, $cid = '')
    {
        return self::_getArticleList("scan_count desc", $limit, $cid, []);
    }

    /**
     * 获取最新文章列表
     *
     * @param integer $limit 要取的文章数目
     * @param string $cid 分类id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getArticleByLatest($limit, $cid = '')
    {
        return self::_getArticleList("id desc", $limit, $cid, []);
    }

    /**
     * 获取flag_recommend文章列表
     *
     * @param $limit
     * @param string $cid
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getArticleByFlagRecommend($limit, $cid = '')
    {
        return self::_getArticleList("id desc", $limit, $cid = '', ['flag_recommend' => 1]);
    }

    /**
     * 获取flag_picture文章列表
     *
     * @param $limit
     * @param string $cid
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getArticleByFlagPicture($limit, $cid = '')
    {
        return self::_getArticleList("id desc", $limit, $cid = '', ['flag_picture' => 1]);
    }

    /**
     * 根据排序、分类等获取文章列表
     *
     * @param $sort
     * @param $limit
     * @param $cid
     * @param array $where
     * @return array|\yii\db\ActiveRecord[]
     */
    private static function _getArticleList($sort, $limit, $cid, $where = [])
    {
        if ($cid != '') {
            $where['cid'] = $cid;
        }
        $where['status'] = ArticleModel::ARTICLE_PUBLISHED;
        $where['type'] = ArticleModel::ARTICLE;
        return ArticleModel::find()->joinWith("category")->orderBy($sort)->where($where)->select([
            'article.id id',
            'cid',
            'title',
            'thumb'
        ])->limit($limit)->asArray()->all();
    }

    /**
     * 获取最热标签
     *
     * @param int $limit
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getHotestTags($limit)
    {
        $metaTagModel = new ArticleMetaTag();
        return $metaTagModel->getHotestTags($limit);
    }

    /**
     * 获取文章列表ActiveDataProvider
     *
     * @param array $where
     * @return \yii\data\ActiveDataProvider
     */
    public static function getArticleList($where = [])
    {
        $where = array_merge($where, ['type' => ArticleModel::ARTICLE, 'status'=>ArticleModel::ARTICLE_PUBLISHED]);
        $query = ArticleModel::find()->select([])->where($where);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'sort' => SORT_ASC,
                    'id' => SORT_DESC,
                ]
            ]
        ]);
        return $dataProvider;
    }

    /**
     * 获取文章列表
     *
     * @param $where
     * @param int $limit
     * @param string $order
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getArticleLists($where, $limit = 4, $order = 'id desc')
    {
        $where = array_merge($where, ['type' => ArticleModel::ARTICLE]);
        return ArticleModel::find()->select([])->where($where)->orderBy($order)->limit($limit)->all();
    }

}