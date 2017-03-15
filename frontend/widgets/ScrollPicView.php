<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-06-19 14:46
 */
namespace frontend\widgets;

use yii\helpers\Url;

class ScrollPicView extends \yii\base\Widget
{

    public $dataProvider;
    public $template = "<ul class='slick centered-btns centered-btns1' style='max-width: 1309px;'>{lis}</ul>
                        <a href='' class=\"centered-btns_nav centered-btns1_nav prev\">Previous</a>
                        <a href='' class=\"centered-btns_nav centered-btns1_nav next\">Next</a>";
    public $liTemplate = "<li id=\"centered-btns1_s0\" class=\"\" style=\"display: list-item; float: none; position: absolute; opacity: 0; z-index: 1; transition: opacity 700ms ease-in-out;\">
                             <a href=\"{article_url}\"><img class=\"img_855x300\" src=\"{img_url}\" alt=\"\"><span></span></a>
                          </li>";

    public function run()
    {
        parent::run();
        $model = $this->dataProvider->getModels();
        $lis = '';
        foreach ($model as $v) {
            $articleUrl = Url::to(['article/view', 'id' => $v->id]);
            $lis .= str_replace(['{article_url}', '{img_url}'], [$articleUrl, $v->thumb], $this->liTemplate);
        }
        return str_replace('{lis}', $lis, $this->template);
    }

}