<?php
/**
 * Ahthor: lf
 * Email: job@feehi.com
 * Blog: http://blog.feehi.com
 * Date: 2016/4/119:53
 */
namespace backend\models;

use Feehi\Upload;
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
            $this->author_id = yii::$app->getUser()->getIdentity()->id;
            $this->author_name = yii::$app->getUser()->getIdentity()->username;

        }
        return parent::beforeSave($insert);
    }

    private function saveThumb($insert)
    {
        if( !isset($_FILES['Article']['name']['thumb']) || $_FILES['Article']['name']['thumb'] == '') {
            unset($this->thumb);
            return true;
        }
        $file = new Upload();
        if ( false != ($uri = $file->upload(yii::getAlias('@thumb'))) ){
            $this->thumb = $uri[0];
            return true;
        } else {
            $this->addError( 'thumb', yii::t('app', 'Upload {attribute} error', ['attribute' => yii::t('app', 'Thumb')]).': '.$uri[0] );
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
    }

    public function beforeDelete()
    {
        Comment::deleteAll(['aid'=>$this->id]);
        if( ($articleContentModel = ArticleContent::find()->where(['aid'=>$this->id])->one()) != null ) {
            $articleContentModel->delete();
        }
        return true;
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->content = ArticleContent::findOne(['aid'=>$this->id])['content'];
    }

}