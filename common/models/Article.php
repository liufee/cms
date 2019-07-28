<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-10-16 17:15
 */

namespace common\models;

use common\models\meta\ArticleMetaImages;
use common\models\meta\ArticleMetaLike;
use common\models\meta\ArticleMetaTag;
use feehi\cdn\TargetAbstract;
use Yii;
use common\libs\Constants;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%article}}".
 *
 * @property integer $id
 * @property integer $cid
 * @property integer $type
 * @property string $title
 * @property string $sub_title
 * @property string $summary
 * @property string $thumb
 * @property string $seo_title
 * @property string $seo_keywords
 * @property string $seo_description
 * @property integer $status
 * @property integer $sort
 * @property integer $author_id
 * @property string $author_name
 * @property integer $scan_count
 * @property integer $comment_count
 * @property integer $can_comment
 * @property integer $visibility
 * @property string $password
 * @property integer $flag_headline
 * @property integer $flag_recommend
 * @property integer $flag_slide_show
 * @property integer $flag_special_recommend
 * @property integer $flag_roll
 * @property integer $flag_bold
 * @property integer $flag_picture
 * @property string  $template
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property ArticleContent $articleContent
 * @property Category $category
 */
class Article extends \yii\db\ActiveRecord
{
    const ARTICLE = 0;
    const SINGLE_PAGE = 2;

    const ARTICLE_PUBLISHED = 1;
    const ARTICLE_DRAFT = 0;

    /**
     * 需要截取的文章缩略图尺寸
     */
    public static $thumbSizes = [
        ["w"=>220, "h"=>150],//首页文章列表
        ["w"=>168, "h"=>112],//精选导读
        ["w"=>185, "h"=>110],//文章详情下边图片推荐
        ["w"=>125, "h"=>86],//热门推荐
    ];

    /**
     * @var array
     */
    public $images;

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%article}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cid', 'type', 'status', 'sort', 'author_id', 'can_comment', 'visibility'], 'integer'],
            [['cid', 'sort', 'author_id'], 'compare', 'compareValue' => 0, 'operator' => '>='],
            [['title', 'status'], 'required'],
            [['can_comment', 'visibility'], 'default', 'value' => Constants::YesNo_Yes],
            [['thumb'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, webp'],
            [['content'], 'string'],
            [['images'], 'safe'],
            [['created_at', 'updated_at'], 'safe'],
            [
                [
                    'title',
                    'sub_title',
                    'summary',
                    'thumb',
                    'seo_title',
                    'seo_keywords',
                    'seo_description',
                    'author_name',
                    'tag',
                    'template'
                ],
                'string',
                'max' => 255
            ],
            [
                [
                    'flag_headline',
                    'flag_recommend',
                    'flag_slide_show',
                    'flag_special_recommend',
                    'flag_roll',
                    'flag_bold',
                    'flag_picture',
                    'status',
                    'can_comment'
                ],
                'in',
                'range' => [0, 1]
            ],
            [['visibility'], 'in', 'range' => array_keys(Constants::getArticleVisibility())],
            [['type'], 'default', 'value'=>self::ARTICLE, 'on'=>'article'],
            [['type'], 'default', 'value'=>self::SINGLE_PAGE, 'on'=>'page'],
            [['password'], 'string', 'max'=>20],
            ['cid', 'default', 'value'=>0]
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'article' => [
                'cid',
                'type',
                'title',
                'sub_title',
                'summary',
                'content',
                'thumb',
                'seo_title',
                'seo_keywords',
                'seo_description',
                'status',
                'sort',
                'author_id',
                'author_name',
                'created_at',
                'updated_at',
                'scan_count',
                'comment_count',
                'can_comment',
                'visibility',
                'tag',
                'flag_headline',
                'flag_recommend',
                'flag_slide_show',
                'flag_special_recommend',
                'flag_roll',
                'flag_bold',
                'flag_picture',
                'password',
                'images',
                'template'
            ],
            'page' => [
                'type',
                'title',
                'sub_title',
                'summary',
                'seo_title',
                'content',
                'seo_keywords',
                'seo_description',
                'status',
                'can_comment',
                'visibility',
                'tag',
                'sort',
                'images',
                'template'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'cid' => Yii::t('app', 'Category Id'),
            'type' => Yii::t('app', 'Type'),
            'title' => Yii::t('app', 'Title'),
            'sub_title' => Yii::t('app', 'Sub Title'),
            'summary' => Yii::t('app', 'Summary'),
            'content' => Yii::t('app', 'Content'),
            'thumb' => Yii::t('app', 'Thumb'),
            'seo_title' => Yii::t('app', 'Seo Title'),
            'seo_keywords' => Yii::t('app', 'Seo Keyword'),
            'seo_description' => Yii::t('app', 'Seo Description'),
            'status' => Yii::t('app', 'Status'),
            'can_comment' => Yii::t('app', 'Can Comment'),
            'visibility' => Yii::t('app', 'Visibility'),
            'sort' => Yii::t('app', 'Sort'),
            'tag' => Yii::t('app', 'Tag'),
            'author_id' => Yii::t('app', 'Author Id'),
            'author_name' => Yii::t('app', 'Author'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'flag_headline' => Yii::t('app', 'Is Headline'),
            'flag_recommend' => Yii::t('app', 'Is Recommend'),
            'flag_special_recommend' => Yii::t('app', 'Is Special Recommend'),
            'flag_slide_show' => Yii::t('app', 'Is Slide Show'),
            'flag_roll' => Yii::t('app', 'Is Roll'),
            'flag_bold' => Yii::t('app', 'Is Bold'),
            'flag_picture' => Yii::t('app', 'Is Picture'),
            'template' => Yii::t('app', 'Article Template'),
            'password' => Yii::t('app', 'Password'),
            'scan_count' => Yii::t('app', 'Scan Count'),
            'comment_count' => Yii::t('app', 'Comment Count'),
            'category' => Yii::t('app', 'Category'),
            'images' => Yii::t('app', 'Article Images'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'cid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleContent()
    {
        return $this->hasOne(ArticleContent::className(), ['aid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleLikes()
    {
        $tempModel = new ArticleMetaLike();
        return $this->hasMany(ArticleMetaLike::className(), ['aid' => 'id'])->where(['key'=>$tempModel->keyName]);
    }

    public function getArticleTags()
    {
        $tempModel = new ArticleMetaTag();
        return $this->hasMany(ArticleMetaLike::className(), ['aid' => 'id'])->where(['key'=>$tempModel->keyName]);
    }

    /**
     * @return integer
     */
    public function getArticleLikeCount()
    {
        return $this->getArticleLikes()->count('id');
    }

    public function afterFind()
    {
        if ($this->thumb) {
            /** @var TargetAbstract $cdn */
            $cdn = Yii::$app->get('cdn');
            $this->thumb = $cdn->getCdnUrl($this->thumb);
        }
        $articleMetaImagesModel = new ArticleMetaImages();
        $this->images = $articleMetaImagesModel->getImagesByArticle($this->id);
        parent::afterFind();
    }

    public function beforeSave($insert)
    {
        if ($this->thumb) {
            /** @var TargetAbstract $cdn */
            $cdn = Yii::$app->get('cdn');
            $this->thumb = str_replace($cdn->host, '', $this->thumb);
        }
        return parent::beforeSave($insert);
    }

    public function getThumbUrlBySize($width='', $height='')
    {
        if( empty($width) || empty($height) ){
            return $this->thumb;
        }
        if( empty($this->thumb) ){//未配图
            return $this->thumb = '/static/images/' . rand(1, 10) . '.jpg';
        }
        static $str = null;
        if( $str === null ) {
            $str = "";
            foreach (self::$thumbSizes as $temp){
                $str .= $temp['w'] . 'x' . $temp['h'] . '---';
            }
        }
        if( strpos($str, $width . 'x' . $height) !== false ){
            $dotPosition = strrpos($this->thumb, '.');
            $thumbExt = "@" . $width . 'x' . $height;
            if( $dotPosition === false ){
                return $this->thumb . $thumbExt;
            }else{
                return substr_replace($this->thumb,$thumbExt, $dotPosition, 0);
            }
        }
        return Yii::$app->getRequest()->getBaseUrl() . '/timthumb.php?' . http_build_query(['src'=>$this->thumb, 'h'=>$height, 'w'=>$width, 'zc'=>0]);
    }
    
}
