<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-11 11:30
 */

namespace backend\models;

use yii;

class ArticleContent extends \common\models\ArticleContent
{

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        parent::beforeSave($insert);
        return true;
    }

    public function replaceToCdnUrl()
    {
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