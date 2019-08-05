<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-02 22:48
 */

namespace frontend\controllers;

use Yii;
use common\libs\Constants;
use frontend\models\form\ArticlePasswordForm;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use frontend\models\Article;
use common\models\Category;
use frontend\models\Comment;
use yii\data\ActiveDataProvider;
use common\models\meta\ArticleMetaLike;
use yii\web\NotFoundHttpException;
use yii\filters\HttpCache;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\XmlResponseFormatter;

class ArticleController extends Controller
{


    public function behaviors()
    {
        return [
            [
                'class' => HttpCache::className(),
                'only' => ['view'],
                'lastModified' => function ($action, $params) {
                    $id = Yii::$app->getRequest()->get('id');
                    $model = Article::findOne(['id' => $id, 'type' => Article::ARTICLE, 'status' => Article::ARTICLE_PUBLISHED]);
                    if( $model === null ) throw new NotFoundHttpException(Yii::t("frontend", "Article id {id} is not exists", ['id' => $id]));
                    Article::updateAllCounters(['scan_count' => 1], ['id' => $id]);
                    if($model->visibility == Constants::ARTICLE_VISIBILITY_PUBLIC) return $model->updated_at;
                },
            ],
        ];
    }

    /**
     * 分类列表页
     *
     * @param string $cat 分类名称
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex($cat = '')
    {
        if ($cat == '') {
            $cat = Yii::$app->getRequest()->getPathInfo();
        }
        $where = ['type' => Article::ARTICLE, 'status' => Article::ARTICLE_PUBLISHED];
        if ($cat != '' && $cat != 'index') {
            if ($cat == Yii::t('app', 'uncategoried')) {
                $where['cid'] = 0;
            } else {
                if (! $category = Category::findOne(['alias' => $cat])) {
                    throw new NotFoundHttpException(Yii::t('frontend', 'None category named {name}', ['name' => $cat]));
                }
                $descendants = Category::getDescendants($category['id']);
                if( empty($descendants) ) {
                    $where['cid'] = $category['id'];
                }else{
                    $cids = ArrayHelper::getColumn($descendants, 'id');
                    $cids[] = $category['id'];
                    $where['cid'] = $cids;
                }
            }
        }
        $query = Article::find()->with('category')->where($where);
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
        $template = "index";
        isset($category) && $category->template != "" && $template = $category->template;
        return $this->render($template, [
            'dataProvider' => $dataProvider,
            'type' => ( !empty($cat) ? Yii::t('frontend', 'Category {cat} articles', ['cat'=>$cat]) : Yii::t('frontend', 'Latest Articles') ),
            'category' => isset($category) ? $category->name : "",
        ]);
    }

    /**
     * 文章详情
     *
     * @param $id
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id)
    {
        /** @var Article $model */
        $model = Article::findOne(['id' => $id, 'type' => Article::ARTICLE, 'status' => Article::ARTICLE_PUBLISHED]);
        if( $model === null ) throw new NotFoundHttpException(Yii::t("frontend", "Article id {id} is not exists", ['id' => $id]));
        $prev = Article::find()
            ->where(['cid' => $model->cid, 'type' => Article::ARTICLE, 'status' => Article::ARTICLE_PUBLISHED])
            ->andWhere(['>', 'id', $id])
            ->orderBy("sort asc,id asc")
            ->limit(1)
            ->one();
        $next = Article::find()
            ->where(['cid' => $model->cid, 'type' => Article::ARTICLE, 'status' => Article::ARTICLE_PUBLISHED])
            ->andWhere(['<', 'id', $id])
            ->orderBy("sort asc,id desc")
            ->limit(1)
            ->one();//->createCommand()->getRawSql();
        $commentModel = new Comment();
        $commentList = $commentModel->getCommentByAid($id);
        $recommends = Article::find()
            ->where(['type' => Article::ARTICLE, 'status' => Article::ARTICLE_PUBLISHED])
            ->andWhere(['<>', 'thumb', ''])
            ->orderBy("rand()")
            ->limit(8)
            ->with('category')
            ->all();
        switch ($model->visibility){
            case Constants::ARTICLE_VISIBILITY_COMMENT://评论可见
                if( Yii::$app->getUser()->getIsGuest() ){
                    $result = Comment::find()->where(['aid'=>$model->id, 'ip'=>Yii::$app->getRequest()->getUserIP()])->one();
                }else{
                    $result = Comment::find()->where(['aid'=>$model->id, 'uid'=>Yii::$app->getUser()->getId()])->one();
                }
                if( $result === null ) {
                    $model->articleContent->content = "<p style='color: red'>" . Yii::t('frontend', "Only commented user can visit this article") . "</p>";
                }
                break;
            case Constants::ARTICLE_VISIBILITY_SECRET://加密文章
                $authorized = Yii::$app->getSession()->get("article_password_" . $model->id, null);
                if( $authorized === null ) $this->redirect(Url::toRoute(['password', 'id'=>$id]));
                break;
            case Constants::ARTICLE_VISIBILITY_LOGIN://登录可见
                if( Yii::$app->getUser()->getIsGuest() ) {
                    $model->articleContent->content = "<p style='color: red'>" . Yii::t('frontend', "Only login user can visit this article") . "</p>";
                }
                break;
        }
        $template = "view";
        isset($model->category) && $model->category->article_template != "" && $template = $model->category->article_template;
        $model->template != "" && $template = $model->template;
        return $this->render($template, [
            'model' => $model,
            'prev' => $prev,
            'next' => $next,
            'recommends' => $recommends,
            'commentModel' => $commentModel,
            'commentList' => $commentList,
        ]);
    }

    /**
     * 获取文章的点赞数和浏览数
     *
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionViewAjax($id)
    {
        $model = Article::findOne($id);
        if( $model === null ) throw new NotFoundHttpException("None exists article id");
        return [
            'likeCount' => (int)$model->getArticleLikeCount(),
            'scanCount' => $model->scan_count * 100,
            'commentCount' => $model->comment_count,
        ];
    }

    /**
     * 评论
     *
     */
    public function actionComment()
    {
        if (Yii::$app->getRequest()->getIsPost()) {
            $commentModel = new Comment();
            if ($commentModel->load(Yii::$app->getRequest()->post()) && $commentModel->save()) {
                $avatar = 'https://secure.gravatar.com/avatar?s=50';
                if ($commentModel->email != '') {
                    $avatar = "https://secure.gravatar.com/avatar/" . md5($commentModel->email) . "?s=50";
                }
                $tips = '';
                if (Yii::$app->feehi->website_comment_need_verify) {
                    $tips = "<span class='c-approved'>" . Yii::t('frontend', 'Comment waiting for approved.') . "</span><br />";
                }
                $commentModel->afterFind();
                return "
                <li class='comment even thread-even depth-1' id='comment-{$commentModel->id}'>
                    <div class='c-avatar'><img src='{$avatar}' class='avatar avatar-108' height='50' width='50'>
                        <div class='c-main' id='div-comment-{$commentModel->id}'><p>{$commentModel->content}</p>
                            {$tips}
                            <div class='c-meta'><span class='c-author'><a href='{$commentModel->website_url}' rel='external nofollow' class='url'>{$commentModel->nickname}</a></span>  (" . Yii::t('frontend', 'a minutes ago') . ")</div>
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
        $aid = Yii::$app->getRequest()->post("aid");
        $model = new ArticleMetaLike();
        $model->setLike($aid);
        return $model->getLikeCount($aid);

    }

    /**
     * rss订阅
     *
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionRss()
    {
        $xml['channel']['title'] = Yii::$app->feehi->website_title;
        $xml['channel']['description'] = Yii::$app->feehi->seo_description;
        $xml['channel']['lin'] = Yii::$app->getUrlManager()->getHostInfo();
        $xml['channel']['generator'] = Yii::$app->getUrlManager()->getHostInfo();
        $models = Article::find()->limit(10)->where(['status'=>Article::ARTICLE_PUBLISHED, 'type'=>Article::ARTICLE])->orderBy('id desc')->all();
        foreach ($models as $model){
            $xml['channel']['item'][] = [
                'title' => $model->title,
                'link' => Url::to(['article/view', 'id'=>$model->id]),
                'pubData' => date('Y-m-d H:i:s', $model->created_at),
                'source' => Yii::$app->feehi->website_title,
                'author' => $model->author_name,
                'description' => $model->summary,
            ];
        }
        Yii::configure(Yii::$app->getResponse(), [
            'formatters' => [
                Response::FORMAT_XML => [
                    'class' => XmlResponseFormatter::className(),
                    'rootTag' => 'rss',
                    'version' => '1.0',
                    'encoding' => 'utf-8'
                ]
            ]
        ]);
        Yii::$app->getResponse()->format = Response::FORMAT_XML;
        return $xml;
    }

}