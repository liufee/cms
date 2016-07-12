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
    {//Yii::$app->mailer->useFileTransport=false;yii::getLogger()->log('ddddddddddddd', \yii\log\Logger::LEVEL_ERROR);
        $searchModel = new ArticleSearch();
        $dataProvider = $searchModel->search(yii::$app->request->queryParams);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionCreate(){
        $model = new Article();
        if( yii::$app->request->isPost ) {
            if ($model->load(yii::$app->request->post()) && $model->validate() && $model->save()) {
                Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->redirect(['index']);
            } else {
                Yii::$app->getSession()->setFlash('error', yii::t('app', 'Error'));
                $errors = $model->getErrors();
                $err = '';
                foreach($errors as $v){
                    $err .= $v[0].'<br>';
                }
                Yii::$app->getSession()->setFlash('reason', $err);
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
            if( $model->load(Yii::$app->request->post()) && $model->save() ){
                Yii::$app->getSession()->setFlash('success', '修改成功');
            }else{
                Yii::$app->getSession()->setFlash('error', '修改失败');
            }
            $model = $this->getModel($id);
        }
        $contentModel = ArticleContent::findOne(['aid'=>$id]);//var_dump($temp);die;
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
            //$contentModel->replaceToCdnUrl();
            $model->content = $contentModel->content;
        }
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function getModel($id = '')
    {
        return Article::findOne(['id'=>$id]);
    }

    public function actionTest()
    {
        echo yii::getRootAlias('@avatar');die;
    }
}