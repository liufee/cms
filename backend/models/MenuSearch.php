<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-11-28 09:29
 */

namespace backend\models;

use yii\data\ArrayDataProvider;

class MenuSearch extends Menu
{

    public function rules()
    {
        return [
            [['name', 'icon', 'url', 'method'], 'string'],
            [['sort', 'is_display'], 'integer'],
        ];
    }

    public function search($params)
    {
        $query = Menu::getMenuArray(Menu::BACKEND_TYPE);
        $this->load($params);
        $temp = explode('\\', self::className());
        $temp = end($temp);
        if (isset($params[$temp])) {
            $serarchArr = $params[$temp];
            foreach ($serarchArr as $k => $v) {
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