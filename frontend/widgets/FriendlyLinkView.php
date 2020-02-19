<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-02-20 00:49
 */

namespace frontend\widgets;


use common\models\FriendlyLink;
use common\services\FriendlyLinkServiceInterface;

class FriendlyLinkView extends \yii\base\Widget
{
    public $data = null;

    public $layout = "{%ITEMS%}";

    public $itemTemplate = "<a target='_blank' href='{%URL%}'>{%NAME%}</a>";

    public function run()
    {
        $items = "";
        $linksModel = $this->getData();
        foreach ($linksModel as $model){
            /** @var FriendlyLink $model */
            $item = str_replace("{%URL%}", $model->url, $this->itemTemplate);
            $item = str_replace("{%NAME%}", $model->name, $item);
            $items .= $item;
        }
        return str_replace("{%ITEMS%}", $items, $this->layout);
    }

    private function getData()
    {
        if( $this->data === null ){
            /** @var FriendlyLinkServiceInterface $friendlyLinkService */
            $friendlyLinkService = \Yii::$app->get(FriendlyLinkServiceInterface::ServiceName);
            $this->data = $friendlyLinkService->getFriendlyLinks();
        }
        return $this->data;
    }
}