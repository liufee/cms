<?php
/**
 * Created by PhpStorm.
 * User: lf
 * Date: 16/4/2
 * Time: 22:48
 */
namespace frontend\controllers;

use yii;
use yii\web\Controller;
use frontend\models\Article;
use yii\web\NotFoundHttpException;

class PageController extends Controller
{

    public function actionView($name='')
    {
        if($name == '') $name = yii::$app->getRequest()->getPathInfo();
        $model = Article::findOne(['sub_title'=>$name]);
        if(empty($model)) throw new NotFoundHttpException('None page named '.$name);
        return $this->render('view', [
            'model' => $model,
        ]);
    }

}