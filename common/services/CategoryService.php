<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-01-30 14:22
 */

namespace common\services;


use common\helpers\FamilyTree;
use common\models\Category;
use yii\base\Exception;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

class CategoryService extends Service implements CategoryServiceInterface
{

    public function getSearchModel(array $query, array $options = [])
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
        $data = Category::getCategories();
        return new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => -1
            ]
        ]);
    }

    public static function getLevelCategories()
    {
        $categories = Category::find()->orderBy(['sort'=>SORT_ASC, 'parent_id'=>SORT_ASC])->all();
        $familyTree = new FamilyTree($categories);
        $array = $familyTree->getDescendants(0);
        return ArrayHelper::index($array, 'id');
    }

    public function getLevelCategoriesWithPrefixLevelCharacters()
    {
        $data = [];
        $categories = $this->getLevelCategories();
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
            $data[$category['id']] = str_repeat($sign, $category['level']-1) . $name;
        }
        return $data;
    }
}