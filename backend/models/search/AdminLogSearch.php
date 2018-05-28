<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-01 23:29
 */

namespace backend\models\search;

use backend\behaviors\TimeSearchBehavior;
use backend\components\search\SearchEvent;
use backend\models\AdminLog;
use Yii;
use yii\data\ActiveDataProvider;

class AdminLogSearch extends \backend\models\AdminLog
{

    public $adminUsername;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description', 'created_at'], 'string'],
            [['user_id'], 'integer'],
            [['route'], 'string', 'max' => 255],
            ['adminUsername', 'safe']
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimeSearchBehavior::className(),
                'timeAttributes' => [AdminLog::tableName() . '.created_at' => 'created_at'],
            ]
        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidConfigException
     */
    public function search($params)
    {
        $query = self::find()->orderBy("id desc");
        $query->joinWith(['user']);
        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = Yii::createObject([
            'class' => ActiveDataProvider::className(),
            'query' => $query,
        ]);
        $dataProvider->setSort([
            'attributes' => [
                'user_username' => [
                    'asc' => ['admin_user.id' => SORT_ASC],
                    'desc' => ['admin_user.id' => SORT_DESC],
                ],
                'id' => [
                    'asc' => ['admin_log.id' => SORT_ASC],
                    'desc' => ['admin_log.id' => SORT_DESC],
                ],
                'created_at' => [
                    'asc' => ['created_at' => SORT_ASC],
                    'desc' => ['created_at' => SORT_DESC],
                ],
                'route' => [
                    'asc' => ['route' => SORT_ASC],
                    'desc' => ['route' => SORT_DESC],
                ],
                'description' => [
                    'asc' => ['description' => SORT_ASC],
                    'desc' => ['description' => SORT_DESC],
                ],
            ]
        ]);
        $this->load($params);
        if (! $this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere(['id' => $this->id])
            ->andFilterWhere(['like', 'route', $this->route])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'username', $this->adminUsername]);
        $this->trigger(SearchEvent::BEFORE_SEARCH, Yii::createObject(['class' => SearchEvent::className(), 'query'=>$query]));
        return $dataProvider;
    }

}