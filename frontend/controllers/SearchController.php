<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-05 13:08
 */

namespace frontend\controllers;

use Yii;
use frontend\controllers\helpers\Helper;
use common\models\meta\ArticleMetaTag;
use common\models\Article;
use yii\helpers\Html;
use yii\web\Controller;
use yii\data\ActiveDataProvider;

class SearchController extends Controller
{

    /**
     * search
     *
     * @return string
     */
    public function actionIndex()
    {
        $where = ['type' => Article::ARTICLE];
        $query = Article::find()->select([])->where($where);
        $keyword = Yii::$app->getRequest()->get('q');
        $query->andFilterWhere(['like', 'title', $keyword]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'sort' => SORT_ASC,
                    'id' => SORT_DESC,
                ]
            ]
        ]);
        $data = array_merge([
            'dataProvider' => $dataProvider,
            'type' => Yii::t('frontend', 'Search keyword {keyword} results', ['keyword'=>Html::encode($keyword)]),
        ], Helper::getCommonInfos());
        return $this->render('/article/index', $data);
    }

    public function actionTag($tag='')
    {
        $metaTagModel = new ArticleMetaTag();
        $aids = $metaTagModel->getAidsByTag($tag);
        $where = ['type' => Article::ARTICLE];
        $query = Article::find()->select([])->where($where)->where(['in', 'id', $aids]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'sort' => SORT_ASC,
                    'id' => SORT_DESC,
                ]
            ]
        ]);
        $data = array_merge([
            'dataProvider' => $dataProvider,
            'type' => Yii::t('frontend', 'Tag {tag} related articles', ['tag'=>Html::encode($tag)]),

        ], Helper::getCommonInfos());
        return $this->render('/article/index', $data);
    }
}
