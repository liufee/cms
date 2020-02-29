<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\models\search;

use Yii;
use backend\behaviors\TimeSearchBehavior;
use backend\components\search\SearchEvent;
use common\models\Article;
use common\models\Category;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class ArticleSearch extends \yii\base\Model implements SearchInterface
{

    public $id;

    public $title;

    public $author_name;

    public $thumb;

    public $cid;

    public $sort;

    public $status;

    public $visibility;

    public $can_comment;

    public $seo_keywords;

    public $password;

    public $sub_title;

    public $summary;

    public $seo_title;

    public $flag_headline;

    public $flag_recommend;

    public $flag_slide_show;

    public $flag_special_recommend;

    public $flag_roll;

    public $flag_bold;

    public $flag_picture;

    public $content;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'author_name', 'cid', 'seo_keywords', 'content', 'sub_title', 'summary', 'seo_title'], 'string'],
            [['created_at', 'updated_at'], 'string'],
            [
                [
                    'id',
                    'status',
                    'flag_headline',
                    'flag_recommend',
                    'flag_slide_show',
                    'flag_special_recommend',
                    'flag_roll',
                    'flag_bold',
                    'flag_picture',
                    'thumb',
                    'sort',
                    'visibility',
                    'can_comment',
                    'password',
                ],
                'integer',
            ],
        ];
    }

    public function behaviors()
    {
        return [
            TimeSearchBehavior::className()
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'cid' => Yii::t('app', 'Category Id'),
            'type' => Yii::t('app', 'Type'),
            'title' => Yii::t('app', 'Title'),
            'sub_title' => Yii::t('app', 'Sub Title'),
            'summary' => Yii::t('app', 'Summary'),
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
     * @param array $params
     * @param array $options
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidConfigException
     */
    public function search(array $params = [], array $options = [])
    {
        $query = Article::find()->select([])->where(['type' => $options['type']])->with('category')->joinWith("articleContent");
        /** @var $dataProvider ActiveDataProvider */
        $dataProvider = Yii::createObject([
            'class' => ActiveDataProvider::className(),
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'sort' => SORT_ASC,
                    'id' => SORT_DESC,
                ]
            ]
        ]);
        $this->load($params);
        if (! $this->validate()) {
            return $dataProvider;
        }
        $query->alias("article")
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['article.id' => $this->id])
            ->andFilterWhere(['status' => $this->status])
            ->andFilterWhere(['flag_headline' => $this->flag_headline])
            ->andFilterWhere(['flag_recommend' => $this->flag_recommend])
            ->andFilterWhere(['flag_slide_show' => $this->flag_slide_show])
            ->andFilterWhere(['flag_special_recommend' => $this->flag_special_recommend])
            ->andFilterWhere(['flag_roll' => $this->flag_roll])
            ->andFilterWhere(['flag_bold' => $this->flag_bold])
            ->andFilterWhere(['flag_picture' => $this->flag_picture])
            ->andFilterWhere(['like', 'author_name', $this->author_name])
            ->andFilterWhere(['sort' => $this->sort])
            ->andFilterWhere(['visibility' => $this->visibility])
            ->andFilterWhere(['can_comment' => $this->can_comment])
            ->andFilterWhere(['like', 'seo_keywords', $this->seo_keywords])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'sub_title', $this->sub_title])
            ->andFilterWhere(['like', 'summary', $this->summary])
            ->andFilterWhere(['like', 'seo_title', $this->seo_title]);
        if ($this->thumb == 1) {
            $query->andWhere(['<>', 'thumb', '']);
        } else {
            if ($this->thumb === '0') {
                $query->andWhere(['thumb' => '']);
            }
        }
        if ($this->password == 1) {
            $query->andWhere(['<>', 'password', '']);
        } else {
            if ($this->password === '0') {
                $query->andWhere(['password' => '']);
            }
        }
        if ($this->cid === '0') {
            $query->andWhere(['cid' => 0]);
        } else {
            if (! empty($this->cid)) {
                $cids = ArrayHelper::getColumn(Category::getDescendants($this->cid), 'id');
                if (count($cids) <= 0) {
                    $query->andFilterWhere(['cid' => $this->cid]);
                } else {
                    $cids[] = $this->cid;
                    $query->andFilterWhere(['cid' => $cids]);
                }
            }
        }
        $this->trigger(SearchEvent::BEFORE_SEARCH, Yii::createObject(['class' => SearchEvent::className(), 'query'=>$query]));
        return $dataProvider;
    }

}