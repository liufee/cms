<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-10-08 00:30
 */

namespace common\models\meta;


use yii\helpers\ArrayHelper;

class ArticleMetaTag extends \common\models\ArticleMeta
{
    public $keyName = 'tag';

    public function getTagsByArticle($aid, $isString=false)
    {
        $result = $this->find()->where(['key'=>$this->keyName])->andWhere(['aid'=>$aid])->asArray()->all();
        if( $result === null ){
            if( $isString ){
                return '';
            }else{
                return [];
            }
        }
        $result = ArrayHelper::getColumn($result, 'value');
        if( $isString ){
            return implode(',', $result);
        }
        return $result;
    }

    public function setArticleTags($aid, $tags)
    {
        if( is_string($tags) ){
            if( empty($tags) ){
                $tags = [];
            }else {
                $tags = str_replace('，', ',', $tags);
                $tags = explode(',', $tags);
            }
        }
        $oldTags = $this->getTagsByArticle($aid);

        $needAdds = array_diff($tags, $oldTags);
        $needRemoves = array_diff($oldTags, $tags);

        foreach ($needAdds as $tag){
            $metaModel = new self([
                'aid' => $aid,
                'key' => $this->keyName,
                'value' => $tag
            ]);
            $metaModel->save();
        }

        foreach ($needRemoves as $tag){
            $this->find()->where(['key'=>$this->keyName])->andwhere(['value'=>$tag])->andwhere(['aid'=>$aid])->one()->delete();
        }
    }

    public function getHottestTags($limit=12)
    {
        $tags = self::find()->select(["value", "count(value) AS times"])->where(["key"=>$this->keyName])->groupBy("value")->limit($limit)->asArray()->all();
        return ArrayHelper::map($tags, 'value', 'times');
    }

    public function getAidsByTag($tag)
    {
        $result = self::find()->where(['ke'=>$this->keyName])->where(['value'=>$tag])->asArray()->all();
        if( $result === null ) return [];
        return ArrayHelper::getColumn($result, 'aid');
    }
}