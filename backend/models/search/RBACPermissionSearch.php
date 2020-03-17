<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-09-15 12:29
 */

namespace backend\models\search;

use backend\models\form\RBACPermissionForm;
use yii\data\ArrayDataProvider;

class RBACPermissionSearch extends RBACPermissionForm implements SearchInterface
{

    public function search(array $params = [], array $options = [])
    {
        $permissions = $options['permissions'];

        if (!$this->load($params)) {
            $sortedPermissions = [];
            foreach ($permissions as $item) {
                $model = new RBACPermissionForm();
                $model->setAttributes($item);
                $sortedPermissions[] = $model;
            }
            $dataProvider = new ArrayDataProvider([
                'allModels' => $sortedPermissions,
                'pagination' => [
                    'pageSize' => -1,
                ],
            ]);
            return $dataProvider;
        }
        $classNameArray = explode('\\', self::className());
        $className = end($classNameArray);
        if (isset($params[$className])) {
            $searchParams = $params[$className];
            foreach ($searchParams as $searchParamKey => $searchParamValue) {
                if ($searchParamValue !== '') {
                    foreach ($permissions as $key => $permission) {
                        if (in_array($searchParamKey, ['sort'])) {
                            if ($permission[$searchParamKey] != $searchParamValue) {
                                unset($permissions[$key]);
                            }
                        } else {
                            if (strpos($permission[$searchParamKey], $searchParamValue) === false) {
                                unset($permissions[$key]);
                            }
                        }
                    }
                }
            }
        }

        $sortedPermissions = [];
        foreach ($permissions as $item) {
            $model = new RBACPermissionForm();
            $model->setAttributes($item);
            $sortedPermissions[] = $model;
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $sortedPermissions,
            'pagination' => [
                'pageSize' => -1,
            ],
        ]);
        return $dataProvider;
    }
}