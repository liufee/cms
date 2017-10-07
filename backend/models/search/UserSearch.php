<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-02 10:07
 */

namespace backend\models\search;

use yii\data\ActiveDataProvider;

class UserSearch extends \backend\models\User
{
    public $create_start_at;

    public $create_end_at;

    public $update_start_at;

    public $update_end_at;

    public function scenarios()
    {
        $scenarios = [self::SCENARIO_DEFAULT => []];
        foreach ($this->getValidators() as $validator) {
            foreach ($validator->on as $scenario) {
                $scenarios[$scenario] = [];
            }
            foreach ($validator->except as $scenario) {
                $scenarios[$scenario] = [];
            }
        }
        $names = array_keys($scenarios);

        foreach ($this->getValidators() as $validator) {
            if (empty($validator->on) && empty($validator->except)) {
                foreach ($names as $name) {
                    foreach ($validator->attributes as $attribute) {
                        $scenarios[$name][$attribute] = true;
                    }
                }
            } elseif (empty($validator->on)) {
                foreach ($names as $name) {
                    if (!in_array($name, $validator->except, true)) {
                        foreach ($validator->attributes as $attribute) {
                            $scenarios[$name][$attribute] = true;
                        }
                    }
                }
            } else {
                foreach ($validator->on as $name) {
                    foreach ($validator->attributes as $attribute) {
                        $scenarios[$name][$attribute] = true;
                    }
                }
            }
        }

        foreach ($scenarios as $scenario => $attributes) {
            if (!empty($attributes)) {
                $scenarios[$scenario] = array_keys($attributes);
            }
        }

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email', 'create_start_at', 'create_end_at', 'update_start_at', 'update_end_at'], 'string'],
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