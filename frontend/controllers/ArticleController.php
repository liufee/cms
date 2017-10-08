<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-02 22:48
 */

namespace frontend\controllers;

use yii;
use common\libs\Constants;
use frontend\models\ArticleContent;
use frontend\models\form\ArticlePasswordForm;
use yii\web\Controller;
use frontend\models\Article;
use common\models\Category;
use frontend\models\Comment;
use yii\data\ActiveDataProvider;
use common\models\meta\ArticleMetaLike;
use yii\web\NotFoundHttpException;
use yii\filters\HttpCache;
use yii\helpers\Url;

class ArticleController extends Controller
{


    public function behaviors()
    {
        return [
            [
                'class' => HttpCache::className(),
                'only' => ['view'],
                'lastModified' => function ($action, $params) {
                    $id = yii::$app->getRequest()->get('id');
                    $article = Article::findOne(['id' => $id]);
                    if( $article === null ) throw new NotFoundHttpException(yii::t("frontend", "Article id {id} is not exists", ['id' => $id]));
                    Article::updateAllCounters(['scan_count' => 1], ['id' => $id]);
                    if($article->visibility == Constants::ARTICLE_VISIBILITY_PUBLIC) return $article->updated_at;
                },
            ],
        ];
    }

    /**
     * 分类列表页
     *
     * @param string $cat 分类名称
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex($cat = '')
    {
        if ($cat == '') {
            $cat = yii::$app->getRequest()->getPathInfo();
        }
        $where = ['type' => Article::ARTICLE, 'status' => Article::ARTICLE_PUBLISHED];
        if ($cat != '' && $cat != 'index') {
            if ($cat == yii::t('app', 'uncategoried')) {
                $where['cid'] = 0;
            } else {
                if (! $category = Category::findOne(['alias' => $cat])) {
                    throw new NotFoundHttpException(yii::t('frontend', 'None category named {name}', ['name' => $cat]));
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
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'type' => ( !empty($cat) ? yii::t('frontend', 'Category {cat} articles', ['cat'=>$cat]) : yii::t('frontend', 'Latest Articles') ),
        ]);
    }

    /**
     * 文章详情页
     *
     * @param integer $id 文章id
     * @return string
     */
    public function actionView($id)
    {
        $model = Article::findOne(['id' => $id]);
        $prev = Article::find()
            ->where(['cid' => $model->cid])
            ->andWhere(['>', 'id', $id])
            ->orderBy("sort asc,created_at asc,id desc")
            ->limit(1)
            ->one();
        $next = Article::find()
            ->where(['cid' => $model->cid])
            ->andWhere(['<', 'id', $id])
            ->orderBy("sort desc,created_at desc,id asc")
            ->limit(1)
            ->one();//->createCommand()->getRawSql();
        $commentModel = new Comment();
        $commentList = $commentModel->getCommentByAid($id);
        $recommends = Article::find()
            ->where(['type' => Article::ARTICLE, 'status' => Article::ARTICLE_PUBLISHED])
            ->andWhere(['<>', 'thumb', ''])
            ->orderBy("rand()")
            ->limit(8)
            ->all();
        switch ($model->visibility){
            case Constants::ARTICLE_VISIBILITY_COMMENT:
                if( yii::$app->getUser()->getIsGuest() ){
                    $result = Comment::find()->where(['aid'=>$model->id, 'ip'=>yii::$app->getRequest()->getUserIP()])->one();
                }else{
                    $result = Comment::find()->where(['aid'=>$model->id, 'uid'=>yii::$app->getUser()->getId()])->one();
                }
                if( $result === null ) {
                    $model->content = "<p style='color: red'>" . yii::t('frontend', "Only commented user can visit this article") . "</p>";
                }else{
                    $model->content = ArticleContent::findOne(['aid'=>$model->id])['content'];
                }
                break;
            case Constants::ARTICLE_VISIBILITY_SECRET:
                $authorized = yii::$app->getSession()->get("article_password_" . $model->id, null);
                if( $authorized === null ) $this->redirect(Url::toRoute(['password', 'id'=>$id]));
                $model->content = ArticleContent::findOne(['aid'=>$model->id])['content'];
                break;
            case Constants::ARTICLE_VISIBILITY_LOGIN:
                if( yii::$app->getUser()->getIsGuest() ) {
                    $model->content = "<p style='color: red'>" . yii::t('frontend', "Only login user can visit this article") . "</p>";
                }else{
                    $model->content = ArticleContent::findOne(['aid'=>$model->id])['content'];
                }
                break;
            default:
                $model->content = ArticleContent::findOne(['aid'=>$model->id])['content'];
                break;

        }
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

    /**
     * 评论
     *
     */
    public function actionComment()
    {
        if (yii::$app->getRequest()->getIsPost()) {
            $commentModel = new Comment();
            if ($commentModel->load(yii::$app->getRequest()->post()) && $commentModel->save()) {
                $avatar = 'https://secure.gravatar.com/avatar?s=50';
                if ($commentModel->email != '') {
                    $avatar = "https://secure.gravatar.com/avatar/" . md5($commentModel->email) . "?s=50";
                }
                $tips = '';
                if (yii::$app->feehi->website_comment_need_verify) {
                    $tips = "<span class='c-approved'>" . yii::t('frontend', 'Comment waiting for approved.') . "</span><br />";
                }
                $commentModel->afterFind();
                return "
                <li class='comment even thread-even depth-1' id='comment-{$commentModel->id}'>
                    <div class='c-avatar'><img src='{$avatar}' class='avatar avatar-108' height='50' width='50'>
                        <div class='c-main' id='div-comment-{$commentModel->id}'><p>{$commentModel->content}</p>
                            {$tips}
                            <div class='c-meta'><span class='c-author'><a href='{$commentModel->website_url}' rel='external nofollow' class='url'>{$commentModel->nickname}</a></span>  (" . yii::t('frontend', 'a minutes ago') . ")</div>
                        </div>
                    </div>";
            } else {
                $temp = $commentModel->getErrors();
                $str = '';
                foreach ($temp as $v) {
                    $str .= $v[0] . "<br>";
                }
                return "<font color='red'>" . $str . "</font>";
            }
        }
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionPassword($id)
    {
        $model = new ArticlePasswordForm();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->checkPassword($id)) {
            return $this->redirect(Url::toRoute(['view', 'id'=>$id]));
        } else {
            return $this->render("password", [
                'model' => $model,
                'article' => Article::findOne($id),
            ]);
        }
    }

    /**
     * 点赞
     *
     * @return int|string
     */
    public function actionLike()
    {
        $aid = yii::$app->getRequest()->post("um_id");
        $model = new ArticleMetaLike();
        $model->setLike($aid);
        return $model->getLikeCount($aid);

    }

}