<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-13 20:25
 */

namespace frontend\models;

use Yii;
use common\models\ArticleContent as CommonModel;


class ArticleContent extends CommonModel
{

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();
        if (! isset(Yii::$app->params['cdnUrl']) || Yii::$app->params['cdnUrl'] == '') {
            return true;
        }
        if (strpos($this->content, 'src="/uploads"')) {
            $pattern = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
            preg_match_all($pattern, $this->content, $matches);
            $matches[1] = array_unique($matches[1]);
            foreach ($matches[1] as $v) {
                $this->content = str_replace($v, Yii::$app->params['cdnUrl'] . $v, $this->content);
            }
        } else {
            $this->content = str_replace(Yii::$app->params['site']['url'], Yii::$app->params['cdnUrl'], $this->content);
        }
        return true;
    }
}