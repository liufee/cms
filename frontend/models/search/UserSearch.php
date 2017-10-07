<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 12:12
 */

namespace frontend\models\search;

use yii\data\ActiveDataProvider;

class UserSearch extends \common\models\User
{
    public $create_start_at;

    public $create_end_at;

    public $update_start_at;

    public $update_end_at;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email', 'create_start_at', 'create_end_at', 'update_start_at', 'update_end_at'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            ['status', 'integer'],
        ];
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
                    'created_at' => SORT_DESC,
                    'updated_at' => SORT_DESC,
                    'username' => SORT_ASC,
                ]
            ]
        ]);
        $this->load($params);
        if (! $this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['=', 'status', $this->status]);
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
        return $dataProvider;
    }

}