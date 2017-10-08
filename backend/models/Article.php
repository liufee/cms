<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-11 09:53
 */

namespace backend\models;

use common\libs\Constants;
use common\models\meta\ArticleMetaTag;
use yii;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

class Article extends \common\models\Article
{

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $upload = UploadedFile::getInstance($this, 'thumb');
        if ($upload !== null) {
            $uploadPath = yii::getAlias('@thumb/');
            if (! FileHelper::createDirectory($uploadPath)) {
                $this->addError('thumb', "Create directory failed " . $uploadPath);
                return false;
            }
            $fullName = $uploadPath . uniqid() . '_' . $upload->baseName . '.' . $upload->extension;
            if (! $upload->saveAs($fullName)) {
                $this->addError('thumb', yii::t('app', 'Upload {attribute} error: ' . $upload->error, ['attribute' => yii::t('app', 'Thumb')]) . ': ' . $fullName);
                return false;
            }
            $this->thumb = str_replace(yii::getAlias('@frontend/web'), '', $fullName);
            if( !$insert ){
                $file = yii::getAlias('@frontend/web') . $this->getOldAttribute('thumb');
                if( file_exists($file) && is_file($file) ) unlink($file);
            }
        } else {
            $this->thumb = $this->getOldAttribute('thumb');
        }
        if ($this->flag_headline == null) {
            $this->flag_headline = 0;
        }
        if ($this->flag_recommend == null) {
            $this->flag_recommend = 0;
        }
        if ($this->flag_slide_show == null) {
            $this->flag_slide_show = 0;
        }
        if ($this->flag_special_recommend == null) {
            $this->flag_special_recommend = 0;
        }
        if ($this->flag_roll == null) {
            $this->flag_roll = 0;
        }
        if ($this->flag_bold == null) {
            $this->flag_bold = 0;
        }
        if ($this->flag_picture == null) {
            $this->flag_picture = 0;
        }
        $this->seo_keywords = str_replace('，', ',', $this->seo_keywords);
        if ($insert) {
            $this->author_id = yii::$app->getUser()->getIdentity()->id;
            $this->author_name = yii::$app->getUser()->getIdentity()->username;

        }
        if($this->visibility == Constants::ARTICLE_VISIBILITY_SECRET){//加密文章需要设置密码
            if( empty( $this->password ) ){
                $this->addError('password', yii::t('app', "Secret article must set a password"));
                return false;
            }
        }else{
            $this->password = '';
        }
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        $articleMetaTag = new ArticleMetaTag();
        $articleMetaTag->setArticleTags($this->id, $this->tag);
        if ($insert) {
            $contentModel = new ArticleContent();
            $contentModel->aid = $this->id;
        } else {
            if ($this->content === null) {
                return;
            }
            $contentModel = ArticleContent::findOne(['aid' => $this->id]);
            if ($contentModel == null) {
                $contentModel = new ArticleContent();
                $contentModel->aid = $this->id;
            }
        }
        $contentModel->content = $this->content;
        $contentModel->save();
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        Comment::deleteAll(['aid' => $this->id]);
        if (($articleContentModel = ArticleContent::find()->where(['aid' => $this->id])->one()) != null) {
            $articleContentModel->delete();
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();
        $metaModel = new ArticleMetaTag();
        $this->tag = $metaModel->getTagsByArticle($this->id, true);
        $this->content = ArticleContent::findOne(['aid' => $this->id])['content'];
    }

}