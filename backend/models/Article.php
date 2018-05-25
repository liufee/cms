<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-11 09:53
 */

namespace backend\models;

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


    public function init()
    {
        parent::init();
        $this->on(self::EVENT_AFTER_VALIDATE, [$this, 'afterValidateEvent']);
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'beforeSaveEvent']);
        $this->on(self::EVENT_BEFORE_UPDATE, [$this, 'beforeSaveEvent']);
        $this->on(self::EVENT_AFTER_INSERT, [$this, 'afterSaveEvent']);
        $this->on(self::EVENT_AFTER_UPDATE, [$this, 'afterSaveEvent']);
        $this->on(self::EVENT_BEFORE_DELETE, [$this, 'beforeDeleteEvent']);
        $this->on(self::EVENT_AFTER_FIND, [$this, 'afterFindEvent']);
    }

    /**
     * @inheritdoc
     */
    public function afterValidateEvent($event)
    {
        if($this->visibility == Constants::ARTICLE_VISIBILITY_SECRET){//加密文章需要设置密码
            if( empty( $event->sender->password ) ){
                $event->sender->addError('password', Yii::t('app', "Secret article must set a password"));
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeSaveEvent($event)
    {
        $insert = $event->sender->getIsNewRecord();
        Util::handleModelSingleFileUpload($event->sender, 'thumb', $insert, '@thumb', ['thumbSizes'=>self::$thumbSizes]);
        $this->seo_keywords = str_replace('，', ',', $this->seo_keywords);
        if ($insert) {
            $this->author_id = Yii::$app->getUser()->getIdentity()->getId();
            $this->author_name = Yii::$app->getUser()->getIdentity()->username;
        }
    }

    /**
     * @inheritdoc
     */
    public function afterSaveEvent($event)
    {
        $articleMetaTag = new ArticleMetaTag();
        $articleMetaTag->setArticleTags($event->sender->id, $event->sender->tag);
        if ($event->sender->getIsNewRecord()) {
            $contentModel = yii::createObject( ArticleContent::className() );
            $contentModel->aid = $event->sender->id;
        } else {
            if ($event->sender->content === null) {
                return;
            }
            $contentModel = ArticleContent::findOne(['aid' => $event->sender->id]);
            if ($contentModel == null) {
                $contentModel = yii::createObject( ArticleContent::className() );
                $contentModel->aid = $event->sender->id;
            }
        }
        $contentModel->content = $event->sender->content;
        $contentModel->save();
    }

    /**
     * @inheritdoc
     */
    public function beforeDeleteEvent($event)
    {
        Comment::deleteAll(['aid' => $this->id]);
        if (($articleContentModel = ArticleContent::find()->where(['aid' => $event->sender->id])->one()) != null) {
            $articleContentModel->delete();
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterFindEvent($event)
    {
        $event->sender->tag = call_user_func(function()use($event){
            $tags = '';
            foreach ($event->sender->articleTags as $tag) {
                $tags .= $tag->value . ',';
            }
            return rtrim($tags, ',');
        });
        $event->sender->content = ArticleContent::findOne(['aid' => $this->id])['content'];
    }

}