<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/30
 * Time: 18:10
 */
namespace api\models;

class Article extends \common\models\Article
{
    public function fields()
    {
        return [
            'title',
            "description" => "summary",
            "content" => function($model){
                return $model->articleContent->content;
            }
        ];
    }
}