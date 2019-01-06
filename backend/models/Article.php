<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-11 09:53
 */

namespace backend\models;

use common\models\meta\ArticleMetaImages;
use Yii;
use common\helpers\Util;
use common\libs\Constants;
use common\models\meta\ArticleMetaTag;

class Article extends \common\models\Article
{
    /**
     * @var string
     */
    public $tag = '';

    /**
     * @var null|string
     */
    public $content = null;


    /**
     * @inheritdoc
     */
    public function afterValidate()
    {
        if($this->visibility == Constants::ARTICLE_VISIBILITY_SECRET){//加密文章需要设置密码
            if( empty( $this->password ) ){
                $this->addError('password', Yii::t('app', "Secret article must set a password"));
            }
        }
        parent::afterValidate();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $insert = $this->getIsNewRecord();
        Util::handleModelSingleFileUpload($this, 'thumb', $insert, '@thumb', ['thumbSizes'=>self::$thumbSizes]);
        $this->seo_keywords = str_replace('，', ',', $this->seo_keywords);
        if ($insert) {
            $this->author_id = Yii::$app->getUser()->getIdentity()->getId();
            $this->author_name = Yii::$app->getUser()->getIdentity()->username;
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
        $articleMetaTag = new ArticleMetaImages();
        $articleMetaTag->setImages($this->id, $this->images);
        if ( $insert ) {
            $contentModel = yii::createObject( ArticleContent::className() );
            $contentModel->aid = $this->id;
        } else {
            if ( $this->content === null ) {
                return true;
            }
            $contentModel = ArticleContent::findOne(['aid' => $this->id]);
            if ($contentModel == null) {
                $contentModel = yii::createObject( ArticleContent::className() );
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
        if( !empty( $this->thumb ) ){
            Util::deleteThumbnails(Yii::getAlias('@frontend/web') . $this->thumb, self::$thumbSizes, true);
        }
        Comment::deleteAll(['aid' => $this->id]);
        if (($articleContentModel = ArticleContent::find()->where(['aid' => $this->id])->one()) != null) {
            $articleContentModel->delete();
        }
        return parent::beforeDelete();
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        $this->tag = call_user_func(function(){
            $tags = '';
            foreach ($this->articleTags as $tag) {
                $tags .= $tag->value . ',';
            }
            return rtrim($tags, ',');
        });
        $this->content = ArticleContent::findOne(['aid' => $this->id])['content'];
        parent::afterFind();
    }

}