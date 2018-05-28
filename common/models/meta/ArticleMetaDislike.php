<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-11-10 10:50
 */

namespace common\models\meta;

use Yii;

class ArticleMetaDislike extends \common\models\ArticleMeta
{

    public $keyName = "dislike";


    /**
     * @param $aid
     * @param int $value
     */
    public function setDislike($aid)
    {
        $this->aid = $aid;
        $this->key = $this->keyName;
        $this->value = Yii::$app->getRequest()->getUserIP();
        $this->save(false);
    }

    /**
     * @param $aid
     * @return int|string
     */
    public function getDislikeCount($aid)
    {
        return $this->find()->where(['aid' => $aid, 'key' => $this->keyName])->count("aid");
    }

}