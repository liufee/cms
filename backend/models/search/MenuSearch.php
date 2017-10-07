<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-11-28 09:29
 */

namespace backend\models\search;

use yii\data\ArrayDataProvider;
use backend\models\Menu;

class MenuSearch extends Menu
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'icon', 'url', 'method'], 'string'],
            [['sort', 'is_display'], 'integer'],
        ];
    }

    /**
     * @param $params
     * @return \yii\data\ArrayDataProvider
     */
    public function search($params)
    {
        $query = Menu::getMenus(Menu::BACKEND_TYPE);
        $this->load($params);
        $temp = explode('\\', self::className());
        $temp = end($temp);
        if (isset($params[$temp])) {
            $searchArr = $params[$temp];
            foreach ($searchArr as $k => $v) {
                if ($v !== '') {
                    foreach ($query as $key => $val) {
                        if (in_array($k, ['sort', 'display'])) {
                            if ($val[$k] != $v) {
                                unset($query[$key]);
                            }
                        } else {
                            if (strpos($val[$k], $v) === false) {
                                unset($query[$key]);
                            }
                        }
                    }
                }
            }
        }
        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
            'pagination' => [
                'pageSize' => -1,
            ],
        ]);
        return $dataProvider;
    }

}