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
use yii\web\Controller;
use yii\data\ActiveDataProvider;

class SearchController extends Controller
{

    public function actionIndex()
    {
        $where = ['type'=>Article::ARTICLE];
        $query = Article::find()->select([])->where($where)->joinWith("category");
        $keyword = htmlspecialchars( yii::$app->request->get('q') );
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
        return $this->render('/article/index', [
            'dataProvider' => $dataProvider
        ]);
    }
}
