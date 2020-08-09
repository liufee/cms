<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-02-20 01:08
 */

namespace frontend\widgets;


use Yii;
use common\models\Comment;
use common\services\CommentServiceInterface;
use yii\helpers\Url;

class LatestCommentView extends \yii\base\Widget
{
    public $data = null;

    public $layout = "<ul>{%ITEMS%}</ul>";

    public $itemTemplate = '<li>
                    <a href="{%HREF%}" title="">
                        <img data-original="{%AVATAR%}" class="avatar avatar-72" height="50"width="50" src="" style="display: block;">
                        <div class="muted">
                            <i>{%NICKNAME%}</i>&nbsp;&nbsp;{%RELATIVE_TIME%}
                            ({%CREATED_AT%}) {%SAID%}
                            ：<br>{%CONTENT%}</div>
                    </a>
                </li>';

    public function run()
    {
        $items = "";
        $models = $this->getData();
        foreach ($models as $model){
            /** @var Comment $model */
            $item = str_replace("{%HREF%}", Url::to(['article/view', 'id' => $model->aid, '#' => 'comment-' . $model->id]), $this->itemTemplate);
            $item = str_replace("{%AVATAR%}", Yii::$app->getRequest()->getBaseUrl() . "/static/images/comment-user-avatar.png", $item);
            $item = str_replace("{%NICKNAME%}", $model->nickname, $item);
            $item = str_replace("{%RELATIVE_TIME%}", Yii::$app->getFormatter()->asRelativeTime($model->created_at), $item);
            $item = str_replace("{%CREATED_AT%}", Yii::$app->getFormatter()->asTime($model->created_at), $item);
            $item = str_replace("{%SAID%}", " " . Yii::t('frontend', 'said'), $item);
            $item = str_replace("{%CONTENT%}", $model->content, $item);
            $items .= $item;
        }
        return str_replace("{%ITEMS%}", $items, $this->layout);
    }

    private function getData()
    {
        if( $this->data === null ){
            /** @var CommentServiceInterface $commentService */
            $commentService = \Yii::$app->get(CommentServiceInterface::ServiceName);
            $this->data = $commentService->getRecentComments();
        }
        return $this->data;
    }
}