<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-05 13:08
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
        $where = ['type' => Article::ARTICLE];
        $query = Article::find()->select([])->where($where)->joinWith("category");
        $keyword = htmlspecialchars(yii::$app->getRequest()->get('q'));
        $query->andFilterWhere(['like', 'title', $keyword])->orFilterWhere(['like', 'tag', $keyword]);
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
