<?php
/**
 * Ahthor: lf
 * Email: job@feehi.com
 * Blog: http://blog.feehi.com
 * Date: 2016/4/119:53
 */
namespace backend\models;

use yii;
use common\models\Article as CommomArticle;
use feehi\libs\File;
use yii\log\Logger;

class Article extends CommomArticle
{

    public function beforeSave($insert)
    {
        if($this->flag_headline == null) $this->flag_headline = 0;
        if($this->flag_recommend == null) $this->flag_recommend = 0;
        if($this->flag_slide_show == null) $this->flag_slide_show = 0;
        if($this->flag_special_recommend == null) $this->flag_special_recommend = 0;
        if($this->flag_roll == null) $this->flag_roll = 0;
        if($this->flag_bold == null) $this->flag_bold = 0;
        if($this->flag_picture == null) $this->flag_picture = 0;
        $this->tag = str_replace( '，', ',', $this->tag);
        $this->seo_keywords = str_replace( '，', ',', $this->seo_keywords);
        if( !$this->saveThumb($insert) ) return false;
        if($insert) {
            $this->created_at = time();
            $this->author_id = yii::$app->user->identity->id;
            $this->author_name = yii::$app->user->identity->username;

        }else {
            $this->updated_at = time();
        }
        return true;
    }

    private function saveThumb($insert)
    {
        if( !isset($_FILES['Article']['name']['thumb']) || $_FILES['Article']['name']['thumb'] == '') {
            unset($this->thumb);
            return true;
        }
        $file = new File();
        $imgs = $file->upload(Yii::getAlias('@thumb'));
        if( $imgs[0] != false ){
            $this->thumb =  yii::$app->params['site']['sign'].str_replace(yii::getAlias('@frontend/web'), '', $imgs[0]);
            yii::$app->alioss->uploadFile($this->thumb, $imgs[0]);
            yii::$app->qiniu->uploadFile($this->thumb, $imgs[0]);
            if(!$insert){
                $oldModel = self::findOne(['id'=>$this->id]);
                if($oldModel != '' && $oldModel->thumb != '') {
                    if (@unlink(Yii::getAlias('@frontend/web').$oldModel->thumb)) yii::getLogger()->log("unlink thumb image failed,article id=>{$this->id},thumb=>{$this->thumb}", Logger::LEVEL_ERROR);
                    yii::$app->alioss->deleteObject($oldModel->thumb);
                    yii::$app->qiniu->deleteObject($oldModel->thumb);
                }

            }
            return true;
        }else{
            yii::getLogger()->log("Article upload to local failed,article title=>{$this->title},error_info=>{$file->getErrors()}", Logger::LEVEL_ERROR);
            $this->addError('thumb', 'Thumb upload to local failed');
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        if($insert){
            $contentModel = new ArticleContent();
            $contentModel->aid = $this->id;
        }else{
            $contentModel = ArticleContent::findOne(['aid'=>$this->id]);
            if($contentModel == null) $contentModel = new ArticleContent();
        }
        $contentModel->content = $this->content;
        $contentModel->save();
    }

}