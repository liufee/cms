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

    public function getSearchModel(array $options = [])
    {
        return new AdminUserSearch();
    }

    public function getModel($id, array $options = [])
    {
        return AdminUser::findOne($id);
    }

    public function newModel(array $options = [])
    {
        $model = new AdminUser();
        $model->setScenario("create");
        $model->loadDefaultValues();
        return $model;
    }

    public function create(array $postData, array $options = [])
    {
        $model = $this->newModel();
        $model->setScenario('create');
        if ( $model->load($postData) && $model->save() ) {
            /** @var RBACServiceInterface $RBACService */
            $RBACService = Yii::$app->get(RBACServiceInterface::ServiceName);
            $result = $RBACService->assignPermission($postData, $model->getId());
            if( $result !== true ){
                Yii::error("create admin user success but assign permission failed:" . print_r($result, true));
            }
            return true;
        } else {
            return $model;
        }
    }

    public function update($id, array $postData, array $options = [])
    {
        $model = $this->getModel($id);
        $scenario = "update";
        $model->setScenario($scenario);
        if ( $model->load($postData) && $model->save() ) {
            /** @var RBACServiceInterface $RBACService */
            $RBACService = Yii::$app->get(RBACServiceInterface::ServiceName);
            $result = $RBACService->assignPermission($postData, $model->getId());
            if( $result !== true ){
                Yii::error("update admin user success but assign permission failed:" . print_r($result, true));
            }
            return true;
        } else {
            return $model;
        }
    }

    public function updateSelf($id, array $postData, array $options = [])
    {
        $model = $this->getModel($id);
        $scenario = "self-update";
        $model->setScenario($scenario);
        if ( $model->load($postData) && $model->save() ) {
            return true;
        } else {
            return $model;
        }
    }
}