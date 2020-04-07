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

class UserService extends Service implements UserServiceInterface
{

    public function getSearchModel(array $options = [])
    {
        return new UserSearch();
    }

    public function getModel($id, array $options = [])
    {
        $model = User::findOne($id);
        if( isset($options['scenario']) && !empty($options['scenario']) ){
            if($model !== null) {
                $model->setScenario($options['scenario']);
            }
        }
        return $model;
    }

    public function newModel(array $options = [])
    {
        $model = new User();
        $model->loadDefaultValues();
        isset($options['scenario']) && $model->setScenario($options['scenario']);
        return $model;
    }

    public function create(array $postData, array $options = [])
    {
        $model = $this->newModel($options);
        if( $model->load($postData) ){
            $model->generateAuthKey();
            $model->setPassword($model->password);
            if( $model->save() ) {
                return true;
            }
        }
        return $model;
    }
}