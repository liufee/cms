<?php
/**
 * Created by PhpStorm.
 * User: lf
 * Date: 16/4/2
 * Time: 22:48
 */
namespace frontend\controllers;

use frontend\models\ArticleContent;
use yii;
use yii\web\Controller;
use frontend\models\Article;
use common\models\Category;
use yii\data\Pagination;
use frontend\models\Comment;

class PageController extends Controller
{


    public function actionView($name='')
    {
        if($name == '') $name = yii::$app->request->pathInfo;
        $model = Article::findOne(['sub_title'=>$name]);
        if(empty($model)) throw new yii\web\NotFoundHttpException;
        $contentModel = ArticleContent::findOne(['aid'=>$model->id]);
        $model->content = $contentModel->content;
        return $this->render('view', [
            'model' => $model,
        ]);
    }

}