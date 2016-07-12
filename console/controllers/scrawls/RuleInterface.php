<?php
/**
 * Ahthor: lf
 * Email: job@feehi.com
 * Blog: http://blog.feehi.com
 * Date: 2016/5/1811:32
 */
namespace console\controllers\scrawls;

interface RuleInterface{

    public function getTotalPage($html);

    public function getListUrl($html);

    public function getArticle($html);

}