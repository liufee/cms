<?php
/**
 * Created by PhpStorm.
 * User: lf
 * Date: 16/4/2
 * Time: 10:07
 */
namespace backend\models;

use common\models\User as UserModel;
use yii\data\ActiveDataProvider;

class UserSearch extends UserModel
{

    public function rules()
    {
        return [
            ['username','string'],
            [['created_at','updated_at'], 'integer'],
            ['email', 'email'],
            ['status', 'integer'],
        ];
    }

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
        if(!$this->validate()){
            return $dataProvider;
        }
        $query->andFilterWhere(['like', 'username', $this->username]);
        return $dataProvider;
    }

}