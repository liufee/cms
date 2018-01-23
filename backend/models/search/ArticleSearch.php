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
use common\models\Article as CommonArticle;
use backend\models\Article;
use common\models\Category;
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
            [['title', 'author_name', 'cid'], 'string'],
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
                    'thumb'
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
     * @return \yii\data\ActiveDataProvider
     */
    public function search($params, $type = self::ARTICLE)
    {
        $query = CommonArticle::find()->select([])->where(['type' => $type])->with('category');
        $dataProvider = new ActiveDataProvider([
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
        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['id' => $this->id])
            ->andFilterWhere(['status' => $this->status])
            ->andFilterWhere(['flag_headline' => $this->flag_headline])
            ->andFilterWhere(['flag_recommend' => $this->flag_recommend])
            ->andFilterWhere(['flag_slide_show' => $this->flag_slide_show])
            ->andFilterWhere(['flag_special_recommend' => $this->flag_special_recommend])
            ->andFilterWhere(['flag_roll' => $this->flag_roll])
            ->andFilterWhere(['flag_bold' => $this->flag_bold])
            ->andFilterWhere(['flag_picture' => $this->flag_picture])
            ->andFilterWhere(['like', 'author_name', $this->author_name]);
        if ($this->thumb == 1) {
            $query->andWhere(['<>', 'thumb', '']);
        } else {
            if ($this->thumb === '0') {
                $query->andWhere(['thumb' => '']);
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
        $this->trigger(SearchEvent::BEFORE_SEARCH, new SearchEvent(['query'=>$query]));
        return $dataProvider;
    }

}