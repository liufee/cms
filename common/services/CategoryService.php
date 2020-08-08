<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-01-30 14:22
 */

namespace common\services;


use common\models\Category;
use yii\base\Exception;
use yii\data\ArrayDataProvider;

class CategoryService extends Service implements CategoryServiceInterface
{

    public function getSearchModel(array $options = [])
    {
        throw new Exception("not need implement");
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
        return new ArrayDataProvider([
            'allModels' => $this->getLevelCategoriesWithPrefixLevelCharacters(),
            'pagination' => [
                'pageSize' => -1
            ]
        ]);
    }

    public function getLevelCategoriesWithPrefixLevelCharacters()
    {
        $data = [];
        $model = $this->newModel();
        $categories = $model->getDescendants(0);
        foreach ($categories as $k => $category){
            /** @var Category $category */
            if( isset($categories[$k+1]['level']) && $categories[$k+1]['level'] == $category['level'] ){
                $name = ' ├' . $category['name'];
            }else{
                $name = ' └' . $category['name'];
            }
            if( end($categories)->id == $category->id ){
                $sign = ' └';
            }else{
                $sign = ' │';
            }
            $category['prefix_level_name'] = str_repeat($sign, $category['level']-1) . $name;
            $data[$category['id']] =$category;
        }
        return $data;
    }

    /**
     * get article categories urls
     *
     * @return array
     */
    public function getCategoriesRelativeUrl()
    {
        $model = $this->newModel();
        $categories = $model->getDescendants(0);
        $data = [];
        foreach ($categories as $k => $category) {
            /** @var Category $category */
            $parents = $category->getAncestors($category['id']);
            $url = '';
            if (!empty($parents)) {
                $parents = array_reverse($parents);
                foreach ($parents as $parent) {
                    $url .= '/' . $parent['alias'];
                }
            }
            if (isset($categories[$k + 1]['level']) && $categories[$k + 1]['level'] == $category['level']) {
                $name = ' ├' . $category['name'];
            } else {
                $name = ' └' . $category['name'];
            }
            if (end($categories)->id == $category->id) {
                $sign = ' └';
            } else {
                $sign = ' │';
            }
            $url = "article/index?cat=" . $category["name"];
            $data[$url] = str_repeat($sign, $category['level'] - 1) . $name;
        }
        return $data;
    }
}