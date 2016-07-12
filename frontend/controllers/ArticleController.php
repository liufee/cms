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
use yii\data\ActiveDataProvider;

class ArticleController extends Controller
{


    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\HttpCache',
                'only' => ['mm'],
                'lastModified' => function ($action, $params) {
                    $article = Article::findOne(['id'=>yii::$app->request->get('id')]);
                    return $article->updated_at;
                },
            ],
        ];
    }

    public function actionIndex($cat='')
    {
        if($cat == '') $cat = yii::$app->request->pathInfo;
        $where = ['type'=>Article::ARTICLE,'status'=>Article::ARTICLE_PUBLISHED];
        if($cat != '' && $cat != 'index') {
            if (!$category = Category::findOne(['name' => $cat])){
                throw new yii\web\NotFoundHttpException('none category named '.$cat);
            }
            $where['cid'] = $category['id'];
        }
        $query = Article::find()->select([])->where($where)->joinWith("category");
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

    public function actionView($id)
    {
        $model = Article::findOne(['id'=>$id]);
        $contentModel = ArticleContent::findOne(['aid'=>$id]);
        $model->content = $contentModel->content;
        /*$pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
        preg_match_all($pattern, $model->content, $matches);
        $matches[1] = array_unique($matches[1]);
        foreach($matches[1] as $v){
            $model->content = str_replace($v, yii::$app->params['fileBaseUrl'].$v, $model->content);
        }*/
        Article::updateAllCounters(['scan_count' => 1], ['id'=>$id]);
        $prev = Article::find()->where(['cid'=>$model->cid])->andWhere(['<', 'id', $id])->limit(1)->one();
        $next = Article::find()->where(['cid'=>$model->cid])->andWhere(['>', 'id', $id])->limit(1)->one();//->createCommand()->getRawSql();
        //$titles = Article::find()->select(['title','id'])->asArray()->column();
        $commentModel = new Comment();
        $commentList = $commentModel->getCommentByAid($id);
        $recommends = Article::find()->orderBy("rand()")->limit(8)->all();
        return $this->render('view', [
            'model' => $model,
            'prev' => $prev,
            'next' => $next,
            'recommends' => $recommends,
            'commentModel' => $commentModel,
            'commentList' => $commentList,
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

    public function actionComment()
    {
        if(yii::$app->request->getIsPost()){
            $commentModel = new Comment();
            if($commentModel->load(yii::$app->request->post()) && $commentModel->save()){
                $avatar = 'http://cd.v7v3.com/avatar/70ca4911ad6f602be5a4e4d834adb883?s=50';
                if($commentModel->email != ''){
                    $avatar = "http://cd.v7v3.com/avatar/".md5($commentModel->email)."?s=50";
                }
                echo "
                <li class='comment even thread-even depth-1' id='comment-{$commentModel->id}'>
                    <div class='c-avatar'><img src='{$avatar}' class='avatar avatar-108' height='50' width='50'>
                        <div class='c-main' id='div-comment-53'><p>{$commentModel->content}</p>
                            <!--<span class='c-approved'>您的评论正在排队审核中，请稍后！</span><br />-->
                            <div class='c-meta'><span class='c-author'><a href='http://dfsdf.com' rel='external nofollow' class='url'>{$commentModel->nickname}</a></span>  (1分钟前)</div>
                        </div>
                    </div>";
            }else{
                var_dump($commentModel->getErrors());die;
            }
        }
    }

}