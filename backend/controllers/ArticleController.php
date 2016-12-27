<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/23
 * Time: 15:13
 */
namespace backend\controllers;

use Yii;
use backend\models\Article;
use backend\models\ArticleSearch;
use backend\models\ArticleContent;

class ArticleController extends BaseController
{

    public function getIndexData()
    {
        $searchModel = new ArticleSearch(['scenario'=>'article']);
        $dataProvider = $searchModel->search(yii::$app->getRequest()->getQueryParams());
        return [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ];
    }

    public function actionViewLayer($id)
    {
        $model = Article::findOne(['id'=>$id]);
        $contentModel = ArticleContent::findOne(['aid'=>$id]);
        $model->content = '';
        if($contentModel != NULL){
            $model->content = $contentModel->content;
        }
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function getModel($id = '')
    {
        if($id == ''){
            $model = new Article();
        }else {
            $model = Article::findOne(['id' => $id]);
            if ($model == null) return null;
        }
        $model->setScenario('article');
        return $model;
    }

}