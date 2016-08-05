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
use common\models\ArticleSearch;
use backend\models\ArticleContent;

class ArticleController extends BaseController
{

    public function actionIndex()
    {
        $searchModel = new ArticleSearch(['scenario'=>'article']);
        $dataProvider = $searchModel->search(yii::$app->request->queryParams);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionCreate(){
        $model = new Article(['scenario'=>'article']);
        if( yii::$app->request->isPost ) {
            if ($model->load(yii::$app->request->post()) && $model->validate() && $model->save()) {
                Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->redirect(['index']);
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach($errors as $v){
                    $err .= $v[0].'<br>';
                }
                Yii::$app->getSession()->setFlash('error', $err);
            }
        }
        $model->loadDefaultValues();
        return $this->render('create', [
            'model' => $model
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->getModel($id);
        if ( Yii::$app->request->isPost ) {
            $model->setScenario('article');
            if( $model->load(Yii::$app->request->post()) && $model->save() ){
                Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
            }else{
                $errors = $model->getErrors();
                $err = '';
                foreach($errors as $v){
                    $err .= $v[0].'<br>';
                }
                Yii::$app->getSession()->setFlash('error', $err);
            }
            $model = $this->getModel($id);
        }
        $contentModel = ArticleContent::findOne(['aid'=>$id]);
        $model->content = $contentModel != NULL ? $contentModel->content : '';
        return $this->render('update', [
            'model' => $model,
        ]);
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
        $model = Article::findOne(['id'=>$id]);
        if($model == null) return null;
        $model->setScenario('article');
        return $model;
    }

}