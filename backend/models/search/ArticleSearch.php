<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\models\search;

use backend\behaviors\TimeSearchBehavior;
use backend\components\search\SearchEvent;
use backend\models\Article;
use common\models\Category;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class ArticleSearch extends Article
{

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

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function behaviors()
    {
        return [
            TimeSearchBehavior::className()
        ];
    }

    /**
     * @param $params
     * @param int $type
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidConfigException
     */
    public function search($params, $type = self::ARTICLE)
    {
        $query = Article::find()->select([])->where(['type' => $type])->with('category')->joinWith("articleContent");
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