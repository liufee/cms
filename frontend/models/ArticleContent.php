<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-13 20:25
 */

namespace frontend\models;

use yii;
use common\models\ArticleContent as CommonModel;


class ArticleContent extends CommonModel
{
    public function afterFind()
    {
        parent::afterFind();
        if (! isset(yii::$app->params['cdnUrl']) || yii::$app->params['cdnUrl'] == '') {
            return true;
        }
        if (strpos($this->content, 'src="/uploads"')) {
            $pattern = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
            preg_match_all($pattern, $this->content, $matches);
            $matches[1] = array_unique($matches[1]);
            foreach ($matches[1] as $v) {
                $this->content = str_replace($v, yii::$app->params['cdnUrl'] . $v, $this->content);
            }
        } else {
            $this->content = str_replace(yii::$app->params['site']['url'], yii::$app->params['cdnUrl'], $this->content);
        }
        return true;
    }
}