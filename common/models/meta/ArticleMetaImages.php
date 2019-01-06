<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2019/1/6 1:55 PM
 */

namespace common\models\meta;


use Yii;
use yii\helpers\ArrayHelper;

class ArticleMetaImages extends \common\models\ArticleMeta
{
    public $keyName = "images";

    /**
     * @param $aid
     * @param $images
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function setImages($aid, $images)
    {
        !is_array($images) && $images = [];
        $oldImages = $this->getImagesByArticle($aid);

        $needAdds = array_diff($images, $oldImages);
        $needRemoves = array_diff($oldImages, $images);

        foreach ($needAdds as $needAdd){
            $metaModel = new self([
                'aid' => $aid,
                'key' => $this->keyName,
                'value' => $needAdd
            ]);
            $metaModel->save();
        }

        foreach ($needRemoves as $needRemove){
            $this->find()->where(['key'=>$this->keyName])->andwhere(['value'=>$needRemove])->andwhere(['aid'=>$aid])->one()->delete();
            if( strpos( strrev($needRemove), '/' ) === 0 ){
                $fullName = Yii::getAlias('@frontend/web') . $needRemove;
            }else{
                $fullName = Yii::getAlias('@frontend/web/') . $needRemove;
            }
            @unlink($fullName);
        }
    }

    /**
     * @param $aid
     * @return int|string
     */
    public function getImagesCount($aid)
    {
        return $this->find()->where(['aid' => $aid, 'key' => $this->keyName])->count("aid");
    }

    public function getImagesByArticle($aid)
    {
        $result = $this->find()->where(['key'=>$this->keyName])->andWhere(['aid'=>$aid])->asArray()->all();
        $result = ArrayHelper::getColumn($result, 'value');
        return $result;
    }
}