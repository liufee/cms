<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-11-28 09:29
 */

namespace backend\models\search;

use backend\behaviors\TimeSearchBehavior;
use common\models\Menu;
use yii\data\ArrayDataProvider;


class MenuSearch extends Menu implements SearchInterface
{

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
        ];
    }

    public function behaviors()
    {
        return [
            TimeSearchBehavior::className()
        ];
    }

    public function search(array $params = [], array $options = [])
    {
        $menus = $options['dataSource'];
        if( !$this->load($params) ) {
            return new ArrayDataProvider([
                'allModels' => $menus,
                'pagination' => [
                    'pageSize' => -1,
                ],
            ]);
        }
        $classNameArray = explode('\\', self::className());
        $className = end($classNameArray);
        if (isset($params[$className])) {
            $searchParams = $params[$className];
            foreach ($searchParams as $searchParamKey => $searchParamValue) {
                if ($searchParamValue !== '') {
                    foreach ($menus as $key => $menu) {
                        if (in_array($searchParamKey, ['sort'])) {
                            if ($menu[$searchParamKey] != $searchParamValue) {
                                unset($menus[$key]);
                            }
                        } else {
                            if (strpos($menu[$searchParamKey], $searchParamValue) === false) {
                                unset($menus[$key]);
                            }
                        }
                    }
                }
            }
        }
        return new ArrayDataProvider([
            'allModels' => $menus,
            'pagination' => [
                'pageSize' => -1,
            ],
        ]);
    }
}