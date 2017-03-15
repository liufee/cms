<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\models;

use yii\data\ActiveDataProvider;
use common\models\Category;

class ArticleSearch extends Article
{

    public $create_start_at;
    public $create_end_at;
    public $update_start_at;
    public $update_end_at;

    public function rules()
    {
        return [
            [['title', 'author_name', 'cid'], 'string'],
            [['created_at', 'updated_at'], 'string'],
            [['create_start_at', 'create_end_at', 'update_start_at', 'update_end_at'], 'string'],
            [
                [
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
                'integer'
            ],
        ];
    }

    public function scenarios()
    {
        $senarios = parent::scenarios();
        $senarios['article'] = array_merge($senarios['article'], [
            'create_start_at',
            'create_end_at',
            'update_start_at',
            'update_end_at'
        ]);
        return $senarios;
    }

    public function search($params, $type = self::ARTICLE)
    {
        $query = Article::find()->select([])->where(['type' => $type]);
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
        if ($this->cid === '0') {
            $query->andWhere(['cid' => 0]);
        } else {
            if (! empty($this->cid)) {
                $cids = Category::getSubTree($this->cid);
                if (count($cids) <= 1) {
                    $query->andFilterWhere(['cid' => $this->cid]);
                } else {
                    $array = [];
                    foreach ($cids as $v) {
                        $array[] = $v['id'];
                    }
                    $query->andFilterWhere(['cid' => $array]);
                }
            }
        }
        return $dataProvider;
    }

}