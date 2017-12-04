<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-06-19 14:46
 */
namespace frontend\widgets;

class ScrollPicView extends \yii\base\Widget
{

    public $banners;

    public $template = "<ul class='slick centered-btns centered-btns1' style='max-width: 1309px;'>{lis}</ul>
                        <a href='' class=\"centered-btns_nav centered-btns1_nav prev\">Previous</a>
                        <a href='' class=\"centered-btns_nav centered-btns1_nav next\">Next</a>";

    public $liTemplate = "<li id=\"centered-btns1_s0\" class=\"\" style=\"display: list-item; float: none; position: absolute; opacity: 0; z-index: 1; transition: opacity 700ms ease-in-out;\">
                             <a target='{target}' href=\"{link_url}\"><img class=\"img_855x300\" src=\"{img_url}\" alt=\"\"><span></span></a>
                          </li>";


    /**
     * @inheritdoc
     */
    public function run()
    {
        parent::run();
        $lis = '';
        foreach ($this->banners as $banner) {
            $lis .= str_replace(['{link_url}', '{img_url}', '{target}'], [$banner['link'], $banner['img'], $banner['target']], $this->liTemplate);
        }
        return str_replace('{lis}', $lis, $this->template);
    }

}