<?php
/**
 * Ahthor: lf
 * Email: job@feehi.com
 * Blog: http://blog.feehi.com
 * Date: 2016/4/119:53
 */
namespace backend\models;

use yii;
use frontend\models\Comment;

class Article extends \common\models\Article
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
        if(!$insert) {//updated thumb should unlink the before picture
           if(!empty($this->oldAttributes['thumb'])) {
               $fileUsageModel = new FileUsage();
               $fileUsageModel->cancelUseFile($this->oldAttributes['thumb'], $this->id, FileUsage::TYPE_ARTICLE_THUMB);
           }
        }
        $model = new File();
        if (($uri = $model->saveFile(FileUsage::TYPE_ARTICLE_THUMB)) !== false) {
            $this->thumb = $uri;
            return true;
        } else {
            $this->addError('thumb', yii::t('app', 'Upload {attribute} error'), ['attribute' => yii::t('app', 'Thumb')]);
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        if($insert){
            $contentModel = new ArticleContent();
            $contentModel->aid = $this->id;
        }else{
            if($this->content === null) return;
            $contentModel = ArticleContent::findOne(['aid'=>$this->id]);
            if($contentModel == null){
                $contentModel = new ArticleContent();
                $contentModel->aid = $this->id;
            }
        }
        $contentModel->content = $this->content;
        $contentModel->save();
        if( isset($this->thumb) ){
            $fileUsageModel = new FileUsage();
            $fileUsageModel->useFile($this->thumb, $this->id);
        }
    }

    public function beforeDelete()
    {
        Comment::deleteAll(['aid'=>$this->id]);
        if( ($articleContentModel = ArticleContent::find()->where(['aid'=>$this->id])->one()) != null ) {
            $articleContentModel->delete();
        }
        if( !empty($this->thumb) ) {
            $fileUsageModel = new FileUsage();
            $fileUsageModel->cancelUseFile($this->thumb, $this->id, FileUsage::TYPE_ARTICLE_THUMB);
        }
        return true;
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->content = ArticleContent::findOne(['aid'=>$this->id])['content'];
    }

}