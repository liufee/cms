<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-05-18 11:32
 */
namespace console\controllers\scrawls;

interface RuleInterface
{

    public function getTotalPage($html);

    public function getListUrl($html);

    public function getArticle($html);

}