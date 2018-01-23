<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-10-16 16:02
 */

namespace backend\models\search;

use backend\behaviors\TimeSearchBehavior;
use backend\components\search\SearchEvent;
use backend\models\FriendlyLink;
use yii\data\ActiveDataProvider;

class FriendlyLinkSearch extends \backend\models\FriendlyLink
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'url'], 'string'],
            [['status', 'image'], 'integer'],
            [['created_at', 'updated_at'], 'string'],
        ];
    }

    public function behaviors()
    {
        return [
            TimeSearchBehavior::className()
        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = FriendlyLink::find();
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
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['status' => $this->status])
            ->andFilterWhere(['like', 'url', $this->url]);
        if ($this->image == 1) {
            $query->andWhere(['<>', 'image', '']);
        } else {
            if ($this->image === '0') {
                $query->andWhere(['image' => '']);
            }
        }
        $this->trigger(SearchEvent::BEFORE_SEARCH, new SearchEvent(['query'=>$query]));
        return $dataProvider;
    }

}