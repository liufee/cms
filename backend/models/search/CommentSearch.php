<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-06-11 22:11
 */

namespace backend\models\search;

use backend\models\Article;
use yii\data\ActiveDataProvider;

class CommentSearch extends \common\models\Comment
{

    public $article_title;

    public $create_start_at;

    public $create_end_at;

    public $update_start_at;

    public $update_end_at;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['article_title'], 'string'];
        unset($rules[1]);
        return $rules;
    }

    /**
     * @param $params
     * @return \yii\data\ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    //'sort' => SORT_ASC,
                    'id' => SORT_DESC,
                ]
            ]
        ]);
        $this->load($params);//var_dump($params);die;
        if (! $this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere(['like', 'nickname', $this->nickname])
            ->andFilterWhere(['status' => $this->status])
            ->andFilterWhere(['aid' => $this->aid])
            ->andFilterWhere(['like', 'content', $this->content]);
        $create_start_at_unixtimestamp = $create_end_at_unixtimestamp = $update_start_at_unixtimestamp = $update_end_at_unixtimestamp = '';
        if ($this->create_start_at != '') {
            $create_start_at_unixtimestamp = strtotime($this->create_start_at);
        }
        if ($this->create_end_at != '') {
            $create_end_at_unixtimestamp = strtotime($this->create_end_at);
        }
        if ($this->update_start_at != '') {
            $update_start_at_unixtimestamp = strtotime($this->update_start_at);
        }
        if ($this->update_end_at != '') {
            $update_end_at_unixtimestamp = strtotime($this->update_end_at);
        }
        if ($create_start_at_unixtimestamp != '' && $create_end_at_unixtimestamp == '') {
            $query->andFilterWhere(['>', 'created_at', $create_start_at_unixtimestamp]);
        } elseif ($create_start_at_unixtimestamp == '' && $create_end_at_unixtimestamp != '') {
            $query->andFilterWhere(['<', 'created_at', $create_end_at_unixtimestamp]);
        } else {
            $query->andFilterWhere([
                'between',
                'created_at',
                $create_start_at_unixtimestamp,
                $create_end_at_unixtimestamp
            ]);
        }

        if ($update_start_at_unixtimestamp != '' && $update_end_at_unixtimestamp == '') {
            $query->andFilterWhere(['>', 'updated_at', $update_start_at_unixtimestamp]);
        } elseif ($update_start_at_unixtimestamp == '' && $update_end_at_unixtimestamp != '') {
            $query->andFilterWhere(['<', 'updated_at', $update_start_at_unixtimestamp]);
        } else {
            $query->andFilterWhere([
                'between',
                'updated_at',
                $update_start_at_unixtimestamp,
                $update_end_at_unixtimestamp
            ]);
        }
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
        if( $this->aid ){
            $this->article_title = Article::findOne($this->aid)['title'];
        }
        return $dataProvider;
    }
}