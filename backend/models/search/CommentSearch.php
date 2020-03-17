<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-06-11 22:11
 */

namespace backend\models\search;

use Yii;
use common\models\Comment;
use backend\behaviors\TimeSearchBehavior;
use backend\components\search\SearchEvent;
use common\models\Article;
use yii\data\ActiveDataProvider;

class CommentSearch extends Comment implements SearchInterface
{

    public $article_title;
    

    public function behaviors()
    {
        return [
            TimeSearchBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['article_title', 'created_at', 'updated_at', 'nickname', 'content'], 'string'],
            [['aid', 'status'], 'integer'],
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
        $query = Comment::find()->with('article');
        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = Yii::createObject([
            'class' => ActiveDataProvider::className(),
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ]
        ]);
        $this->load($params);
        if (! $this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere(['like', 'nickname', $this->nickname])
            ->andFilterWhere(['status' => $this->status])
            ->andFilterWhere(['aid' => $this->aid])
            ->andFilterWhere(['like', 'content', $this->content]);

        if ($this->article_title != '') {
            $articles = Article::find()
                ->where(['like', 'title', $this->article_title])
                ->select(['id', 'title'])
                ->indexBy('id')
                ->asArray()
                ->all();
            $aidArray = [];
            foreach ($articles as $k => $v) {
                array_push($aidArray, $k);
            }
            $query->andFilterWhere(['aid' => $aidArray]);
        }

        $this->trigger(SearchEvent::BEFORE_SEARCH, Yii::createObject(['class' => SearchEvent::className(), 'query'=>$query]));
        return $dataProvider;
    }
}