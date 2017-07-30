<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-05-18 11:31
 */
namespace console\controllers\scrawls;

use yii\console\Exception;
use common\libs\SimpleHtmlDom;

error_reporting(0);

class Jobbole implements RuleInterface
{


    public function getTotalPage($html)
    {
        $obj = new SimpleHtmlDom($html);
        $lis = $obj->find(".page-numbers");
        $key = count($lis) - 2;
        $totalPage = $lis[$key]->plaintext;
        if (! is_numeric($totalPage)) {
            throw new Exception("Get total page error:$totalPage");
        }
        return $totalPage;
    }

    public function getListUrl($html)
    {
        $obj = new SimpleHtmlDom($html);
        $divs = $obj->find(".grid-8 .floated-thumb");
        $urls = [];
        foreach ($divs as $div) {//var_dump($div);die;
            $temp = $div->find('p')[0]->innertext;
            preg_match("/<br \/>(.*?)<a/", $temp, $matches);
            $created_at = strtotime(str_replace(['&middot;', '|'], ['', ''], $matches[1]));
            array_unshift($urls, [$div->find('a')[0]->href, $div->find('img')[0]->src, $created_at]);
        }
        return $urls;
    }

    public function getArticle($html)
    {//file_put_contents('a.txt', $html);exit;
        $obj = new SimpleHtmlDom($html);
        $data = [];//var_dump($obj->find(".entry-header h1")[0]->plaintext);exit;
        $data['title'] = $obj->find(".entry-header h1")[0]->plaintext;//标题
        $data['content'] = $obj->find("div.entry")[0]->innertext;//文章内容
        $data['content'] = preg_replace("/<div class=\'copyright-area\'>.*?<\/div>/", '', $data['content']);
        $temp = explode('<script language="javascript">', $data['content']);
        $data['content'] = $temp[0];
        $temp = $obj->find(".entry-meta p.entry-meta-hide-on-mobile");
        $data['tag'] = '';
        if (! empty($temp)) {
            if (strpos('标签', $temp[0]->innertext)) {//java文章
                $temp = explode('标签', $temp[0]->innertext);
                if (isset($temp[1])) {
                    preg_match_all("/<a.*?>(.*?)<\/a>/", $temp[1], $matches);
                    if (isset($matches[1])) {
                        $data['tag'] = implode(',', $matches[1]);
                        unset($matches);
                    }
                }
            } else {//python,web文章
                $temp = $obj->find(".entry-meta p.entry-meta-hide-on-mobile a");
                $tempArr = [];
                foreach ($temp as $key => $value) {
                    $t = $value->plaintext;
                    $tArr = [];
                    if (strpos('评论', $t) === false) {
                        array_push($tArr, $t);
                    }
                }
                if (! empty($tArr)) {
                    $data['tag'] = implode(',', $tArr);
                }
            }
        }
        $obj->clear();
        preg_match("/<meta name=[\"\']description[\"\'] content=[\"\'](.*)[\"\']/", $html, $matches);
        $data['seo_description'] = $matches[1];
        return $data;
    }

}
