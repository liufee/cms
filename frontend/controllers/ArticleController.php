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
use common\models\Category;
use frontend\models\Comment;
use yii\data\ActiveDataProvider;
use common\models\ArticleMetaLike;

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
            if($cat == yii::t('app', 'uncategoried')){
                $where['cid'] = 0;
            }else {
                if (!$category = Category::findOne(['name' => $cat])) {
                    throw new yii\web\NotFoundHttpException('None category named ' . $cat);
                }
                $where['cid'] = $category['id'];
            }
        }
        $query = Article::find()->select([])->where($where);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'sort' => SORT_ASC,
                    'created_at' => SORT_DESC,
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
        Article::updateAllCounters(['scan_count' => 1], ['id'=>$id]);
        $prev = Article::find()->where(['cid'=>$model->cid])->andWhere(['>', 'id', $id])->orderBy("sort asc,created_at asc,id desc")->limit(1)->one();
        $next = Article::find()->where(['cid'=>$model->cid])->andWhere(['<', 'id', $id])->orderBy("sort desc,created_at desc,id asc")->limit(1)->one();//->createCommand()->getRawSql();
        $commentModel = new Comment();
        $commentList = $commentModel->getCommentByAid($id);
        $recommends = Article::find()->where(['type'=>Article::ARTICLE, 'status'=>Article::ARTICLE_PUBLISHED])->andWhere(['<>', 'thumb', ''])->orderBy("rand()")->limit(8)->all();
        $likeModel = new ArticleMetaLike();
        return $this->render('view', [
            'model' => $model,
            'likeCount' => $likeModel->getLikeCount($id),
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
                $avatar = 'https://secure.gravatar.com/avatar?s=50';
                if($commentModel->email != ''){
                    $avatar = "https://secure.gravatar.com/avatar/".md5($commentModel->email)."?s=50";
                }
                $tips = '';
                if(yii::$app->feehi->website_comment_need_verify){
                    $tips = "<span class='c-approved'>您的评论正在排队审核中，请稍后！</span><br />";
                }
                $commentModel->content = str_replace([':mrgreen:',':razz:',':sad:',':smile:',':oops:',':grin:',':eek:',':???:',':cool:',':lol:',':mad:',':twisted:',':roll:',':wink:',':idea:',':arrow:',':neutral:',':cry:',':?:',':evil:',':shock:',':!:'],
                    ["<img src='{%URL%}mrgreen{%EXT%}'>","<img src='{%URL%}razz{%EXT%}'>","<img src='{%URL%}sad{%EXT%}'>","<img src='{%URL%}smile{%EXT%}'>","<img src='{%URL%}redface{%EXT%}'>","<img src='{%URL%}biggrin{%EXT%}'>","<img src='{%URL%}surprised{%EXT%}'>","<img src='{%URL%}confused{%EXT%}'>","<img src='{%URL%}cool{%EXT%}'>","<img src='{%URL%}lol{%EXT%}'>","<img src='{%URL%}mad{%EXT%}'>","<img src='{%URL%}twisted{%EXT%}'>","<img src='{%URL%}rolleyes{%EXT%}'>","<img src='{%URL%}wink{%EXT%}'>","<img src='{%URL%}idea{%EXT%}'>","<img src='{%URL%}arrow{%EXT%}'>","<img src='{%URL%}neutral{%EXT%}'>","<img src='{%URL%}cry{%EXT%}'>","<img src='{%URL%}question{%EXT%}'>","<img src='{%URL%}evil{%EXT%}'>","<img src='{%URL%}eek{%EXT%}'>","<img src='{%URL%}exclaim{%EXT%}'>"],$commentModel->content);
                $commentModel->content = str_replace(['{%URL%}', '{%EXT%}'], [yii::$app->homeUrl.'static/images/smilies/icon_', '.gif'], $commentModel->content);
                echo "
                <li class='comment even thread-even depth-1' id='comment-{$commentModel->id}'>
                    <div class='c-avatar'><img src='{$avatar}' class='avatar avatar-108' height='50' width='50'>
                        <div class='c-main' id='div-comment-53'><p>{$commentModel->content}</p>
                            {$tips}
                            <div class='c-meta'><span class='c-author'><a href='{$commentModel->website_url}' rel='external nofollow' class='url'>{$commentModel->nickname}</a></span>  (1分钟前)</div>
                        </div>
                    </div>";
            }else{
                $temp = $commentModel->getErrors();
                $str = '';
                foreach($temp as $v){
                    $str .= $v[0]."<br>";
                }
                echo "<font color='red'>".$str."</font>";
            }
        }
    }

    public function actionLike()
    {
        $aid = yii::$app->getRequest()->post("um_id");
        $model = new \common\models\ArticleMetaLike();
        $model->setLike($aid);
        echo $model->getLikeCount($aid);

    }

}