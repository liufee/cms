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

    private function getSimilar($title,$arr_title)
    {
        $arr_len = count($arr_title);
        for($i=0; $i<=($arr_len-1); $i++)
        {
            //取得两个字符串相似的字节数
            $arr_similar[$i] = similar_text($arr_title[$i],$title);
        }
        arsort($arr_similar);	//按照相似的字节数由高到低排序
        reset($arr_similar);	//将指针移到数组的第一单元
        $index = 0;
        foreach($arr_similar as $old_index=>$similar)
        {
            $new_title_array[$index] = $arr_title[$old_index];
            $index++;
        }
        return $new_title_array;
    }

}