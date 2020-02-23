<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-01-30 14:22
 */

namespace common\services;


use common\models\Category;
use yii\data\ArrayDataProvider;

class CategoryService extends Service implements CategoryServiceInterface
{

    public function getSearchModel(array $query, array $options = [])
    {
        // TODO: Implement getSearchModel() method.
    }

    public function getModel($id, array $options = [])
    {
        return Category::findOne($id);
    }

    public function newModel(array $options = [])
    {
        return new Category();
    }

    public function getCategoryList()
    {
        $data = Category::getCategories();
        return new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => -1
            ]
        ]);
    }
}