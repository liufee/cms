<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-01-30 14:21
 */

namespace common\services;


interface CategoryServiceInterface extends ServiceInterface
{
    const ServiceName = "categoryService";

    public function getCategoryList();

    public function getLevelCategoriesWithPrefixLevelCharacters();
}