<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-02-20 10:13
 */

namespace frontend\widgets;


use Yii;
use common\models\Comment;
use common\services\CommentServiceInterface;
use yii\helpers\Url;

class RecentCommentArticleView extends \yii\base\Widget
{
    public $data = null;

    public $layout = "<ul>{%ITEMS%}</ul>";

    public $itemTemplate = '<li>
                    <a href="{%URL%}" title="{%TITLE%}">
                        <span class="thumbnail"><img src="{%IMG_URL%}" alt="{%TITLE%}"></span>
                        <span class="text">{%TITLE%}</span>
                        <span class="muted">{%CREATED_AT%}</span><span class="muted_1">{%COMMENT_COUNT%}{%COMMENT%}</span>
                    </a>
                </li>';

    public function run()
    {
        $items = "";
        $linksModel = $this->getData();
        foreach ($linksModel as $model){
            /** @var Comment $model */
            $item = str_replace("{%URL%}",  $url = Url::to(['article/view', 'id' => $model->id]), $this->itemTemplate);
            $item = str_replace("{%IMG_URL%}", $model->article->getThumbUrlBySize(125, 86), $item);
            $item = str_replace("{%TITLE%}", $model->article->title, $item);
            $item = str_replace("{%CREATED_AT%}", Yii::$app->getFormatter()->asDate($model->article->created_at), $item);
            $item = str_replace("{%COMMENT_COUNT%}", $model->article->comment_count, $item);
            $item = str_replace("{%COMMENT%}", Yii::t('frontend', ' Comments'), $item);
            $items .= $item;
        }
        return str_replace("{%ITEMS%}", $items, $this->layout);
    }

    private function getData()
    {
        if( $this->data === null ){
            /** @var CommentServiceInterface $commentService */
            $commentService = \Yii::$app->get(CommentServiceInterface::ServiceName);
            $this->data = $commentService->getRecentComments(8);
        }
        return $this->data;
    }
}