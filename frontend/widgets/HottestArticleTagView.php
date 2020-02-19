<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-02-20 01:30
 */

namespace frontend\widgets;


use Yii;
use common\models\meta\ArticleMetaTag;
use yii\helpers\Url;

class HottestArticleTagView extends \yii\base\Widget
{
    public $data = null;

    public $layout = "{%ITEMS%}";

    public $itemTemplate = "<a title='' href='{%HREF%}' data-original-title='{%TAG_NUM%}{%TOPICS%}' {%TAG_NAME%} ({%TAG_NUM%})</a>";

    public function run()
    {
        $items = "";
        $data = $this->getData();
        foreach ($data as $tagName => $num){
            /** @var ArticleMetaTag $model */
            $item = str_replace("{%HREF%}", Url::to(['search/tag', 'tag' => $tagName]), $this->itemTemplate);
            $item = str_replace("{%TAG_NUM%}", $num, $item);
            $item = str_replace("{%TOPICS%}", Yii::t('frontend', ' Topics'), $item);
            $items .= $item;
        }
        return str_replace($this->layout, "{%ITEMS%}", $items);
    }

    private function getData()
    {
        if( $this->data === null ){
            $tagsModel = new ArticleMetaTag();
            $this->data = $tagsModel->getHotestTags();
        }
        return $this->data;
    }
}