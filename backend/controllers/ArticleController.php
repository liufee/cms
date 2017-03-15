<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-23 15:13
 */

namespace backend\controllers;

use yii;
use backend\models\Article;
use backend\models\ArticleSearch;
use backend\models\ArticleContent;

class ArticleController extends BaseController
{

    public function getIndexData()
    {
        $searchModel = new ArticleSearch(['scenario' => 'article']);
        $dataProvider = $searchModel->search(yii::$app->getRequest()->getQueryParams());
        return [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ];
    }

    public function actionViewLayer($id)
    {
        $model = Article::findOne(['id' => $id]);
        $contentModel = ArticleContent::findOne(['aid' => $id]);
        $model->content = '';
        if ($contentModel != null) {
            $model->content = $contentModel->content;
        }
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function getModel($id = '')
    {
        if ($id == '') {
            $model = new Article();
        } else {
            $model = Article::findOne(['id' => $id]);
            if ($model == null) {
                return null;
            }
        }
        $model->setScenario('article');
        return $model;
    }

}