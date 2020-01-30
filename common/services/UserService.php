<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-01-29 16:54
 */

namespace common\services;


use backend\models\search\UserSearch;
use common\models\User;

class UserService extends Service implements ServiceInterface
{

    public function getSearchModel(array $query, array $options = [])
    {
        return new UserSearch();
    }

    public function getModel($id, array $options = [])
    {
        return User::findOne($id);
    }

    public function getNewModel(array $options = [])
    {
        $model = new User();
        $model->loadDefaultValues();
        return $model;
    }
}