<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-01-29 15:31
 */

namespace common\services;


use backend\models\search\AdminUserSearch;
use common\models\AdminUser;
use Yii;

class AdminUserService extends Service implements AdminUserServiceInterface
{

    public function getSearchModel(array $query, array $options = [])
    {
        return new AdminUserSearch();
    }

    public function getModel($id, array $options = [])
    {
        return AdminUser::findOne($id);
    }

    public function getNewModel(array $options = [])
    {
        return new AdminUser();
    }

    public function create(array $postData, array $options = [])
    {
        $model = $this->getNewModel();
        $model->setScenario('create');
        if ( $model->load($postData) && $model->save() && $model->assignPermission() ) {
          return true;
        } else {
            return $model->getErrors();
        }
    }

    public function update($id, array $postData, array $options = [])
    {
        $model = $this->getModel($id);
        $model->setScenario('update');
        $model->roles = $model->permissions = call_user_func(function() use($id){
            $permissions = Yii::$app->getAuthManager()->getAssignments($id);
            foreach ($permissions as $k => &$v){
                $v = $k;
            }
            return $permissions;
        });
        return $model;
    }
}