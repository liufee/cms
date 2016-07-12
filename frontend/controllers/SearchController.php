<?php
/**
 * Ahthor: lf
 * Email: job@feehi.com
 * Blog: http://blog.feehi.com
 * Date: 2016/4/513:08
 */
namespace frontend\controllers;

use yii;
use frontend\models\Article;
use yii\data\Pagination;
use yii\web\Controller;
use yii\data\ActiveDataProvider;

class SearchController extends Controller
{

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        /*actionIndex≥,m
        $query = Article::find()->where(['status'=>Article::ARTICLE_PUBLISHED])->joinWith("category");//->createCommand()->getRawSql();echo $query;die;
        $keyword = yii::$app->request->get('q');//echo $keyword;die;
        $query->andFilterWhere(['like', 'title', $keyword])
            ->orFilterWhere(['like', 'tag', $keyword]);
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize'=>3]);
        $model = $query->orderBy('id desc')->offset($pages->offset)
            ->limit($pages->limit)
            ->all();//var_dump($model[0]['category']['name']);die;
        return $this->render('/site/index', [
            'model' => $?.,model,
            'pages' => $pages,
            'type' => '搜索',
        ]);*/
        $where = ['type'=>Article::ARTICLE];
        $query = Article::find()->select([])->where($where)->joinWith("category");
        $keyword = yii::$app->request->get('q');//echo $keyword;die;
        $query->andFilterWhere(['like', 'title', $keyword])
            ->orFilterWhere(['like', 'tag', $keyword]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'sort' => SORT_ASC,
                    'id' => SORT_DESC,
                ]
            ]
        ]);
        return $this->render('/site/index', [
            'dataProvider' => $dataProvider
        ]);
    }
}
