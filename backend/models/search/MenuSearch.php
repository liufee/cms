<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-11-28 09:29
 */

namespace backend\models\search;

use Yii;
use backend\behaviors\TimeSearchBehavior;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use backend\models\Menu;
use backend\components\search\SearchEvent;
use yii\data\ArrayDataProvider;


class MenuSearch extends \yii\base\Model
{

    public $name;

    public $url;

    public $sort;

    public $target;

    public $is_display;

    public $created_at;

    public $updated_at;

    public $is_absolute_url;

    public function attributes()
    {
        return [
            "name", "url", "sort", "target", "is_display", "is_absolute_url"
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'url', "target"], 'string'],
            [['sort', 'is_display', "is_absolute_url"], 'integer'],
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
     * @param $options
     * @return ArrayDataProvider
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function search($params, array $options = [])
    {
        if (!isset($options["type"]) || !in_array($options['type'], [Menu::TYPE_BACKEND, Menu::TYPE_FRONTEND])){
            throw new Exception("Menu search must set options['type']");
        }
        $menuType = $options['type'];
        $query = Menu::find();
        $query->andWhere(["type" => $menuType]);
        $dataProvider = new ArrayDataProvider([
            'allModels' => $query->all(),
            'pagination' => [
                'pageSize' => -1,
            ],
        ]);
        if( !$this->load($params) ) {
            return $dataProvider;
        }
        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'url', $this->url]);
        $query->andFilterWhere(['target' => $this->target]);
        $query->andFilterWhere(['sort' => $this->sort]);
        $query->andFilterWhere(['is_display' => $this->is_display]);
        $query->andFilterWhere(['is_absolute_url' => $this->is_absolute_url]);
        $this->trigger(SearchEvent::BEFORE_SEARCH, Yii::createObject(['class' => SearchEvent::className(), 'query' => $query]));
        $dataProvider->allModels = $query->all();
        return $dataProvider;
    }
}