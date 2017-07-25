<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-04 16:53
 */

namespace frontend\controllers\components;

use yii\base\Object;
use yii\data\ActiveDataProvider;
use common\models\Menu;
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

    public static function _getArticleList($sort, $limit, $cid, $where = [])
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
     * 获取标签
     *
     * @param int $limit
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getTags($limit = 14)
    {
        $data = ArticleModel::find()->select('tag')->where(['<>', 'tag', ''])->asArray()->all();
        $tags = [];//var_dump($data);die;
        foreach ($data as $val) {//var_dump($val);die;
            $tags = array_merge($tags, explode(',', $val['tag']));
        }
        shuffle($tags);
        $data = array_slice(array_count_values($tags), 0, $limit);
        return $data;
    }

    public static function getArticleList($where = [])
    {
        $where = array_merge($where, ['type' => ArticleModel::ARTICLE]);
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

    public static function getArticleLists($where, $limit = 4, $order = 'id desc')
    {
        $where = array_merge($where, ['type' => ArticleModel::ARTICLE]);
        return ArticleModel::find()->select([])->where($where)->orderBy($order)->limit($limit)->all();
    }

    public static function getMenuDataProvider()
    {
        $query = Menu::find()
            ->select([])
            ->where(['type' => \common\models\Menu::FRONTEND_TYPE])
            ->orderBy('parent_id asc');
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

}