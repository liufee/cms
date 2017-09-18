<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-09-12 11:30
 */

namespace backend\controllers;

use yii;
use backend\form\RbacSearch;
use backend\form\Rbac;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class RbacController extends \yii\web\Controller
{
    public function actionPermissions()
    {
        $searchModel = new RbacSearch(['scenario'=>'permission']);
        $dataProvider = $searchModel->searchPermissions(yii::$app->getRequest()->getQueryParams());
        return $this->render('permissions', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionPermissionsSort()
    {
        if (yii::$app->getRequest()->getIsPost()) {
            $authManager = yii::$app->getAuthManager();
            $data = yii::$app->getRequest()->post();
            if (! empty($data['sort'])) {
                foreach ($data['sort'] as $key => $value) {
                    $model = new Rbac(['scenario'=>'permission']);
                    $model = $model->fillModel($key);
                    if ($model->sort != $value) {
                        $role = $authManager->getPermission($key);
                        $data = json_decode($role->data, true);
                        $data['sort'] = $value;
                        $role->data = json_encode( $data );
                        yii::$app->getAuthManager()->update($key, $role);
                    }
                }
            }
        }
        $this->redirect(['permissions']);
    }

    public function actionPermissionCreate()
    {
        $model = new Rbac(['scenario'=>'permission']);
        if( yii::$app->getRequest()->getIsPost() ) {
            if ($model->load(yii::$app->getRequest()->post()) && $model->validate() && $model->createPermission()) {
                yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->redirect(['permissions']);
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                Yii::$app->getSession()->setFlash('error', $err);
            }
        }
        return $this->render('permission-create', [
            'model' => $model,
        ]);
    }

    public function actionPermissionUpdate($name)
    {
        $model = new Rbac(['scenario'=>'permission']);
        $permission = $model->fillModel($name);
        $model = new Rbac($permission);
        $model->setScenario('permission');
        if( yii::$app->getRequest()->getIsPost() ) {
            if ($model->load(yii::$app->getRequest()->post()) && $model->validate() && $model->updatePermission($name)) {
                yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->redirect(['permissions']);
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                Yii::$app->getSession()->setFlash('error', $err);
            }
        }
        return $this->render('permission-update', [
            'model' => $model,
        ]);
    }

    public function actionPermissionDelete($name='')
    {
        $authManager = yii::$app->getAuthManager();
        if ($name == '') {
            Yii::$app->getResponse()->format = Response::FORMAT_JSON;
            $id = yii::$app->getRequest()->get('id', '');
            if (! $id) {
                return ['code' => 1, 'message' => yii::t('app', "Name doesn't exit")];
            }
            $ids = explode(',', $id);
            $errorIds = [];
            $model = null;
            foreach ($ids as $one) {
                $permission = $authManager->getPermission($one);
                if (! $authManager->remove($permission)) {
                    $errorIds[] = $one;
                }
            }
            if (count($errorIds) == 0) {
                return ['code' => 0, 'message' => yii::t('app', 'Success')];
            } else {
                return ['code' => 1, 'message' => 'id ' . implode(',', $errorIds) . yii::t('app', 'Error')];
            }
        }else {
            $permission = $authManager->getPermission($name);
            if ($authManager->remove($permission)) {
                return ['code' => 0, 'message' => yii::t('app', 'Success')];
            } else {
                return ['code' => 1, 'message' => yii::t('app', 'Error')];
            }
        }
    }

    public function actionRoles()
    {
        $searchModel = new RbacSearch(['scenario'=>'role']);
        $dataProvider = $searchModel->searchRoles(yii::$app->getRequest()->getQueryParams());
        return $this->render('roles', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionRoleCreate()
    {
        $model = new Rbac(['scenario'=>'role']);
        if( yii::$app->getRequest()->getIsPost() ) {
            if ($model->load(yii::$app->getRequest()->post()) && $model->validate() && $model->createRole()) {
                yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->redirect(['roles']);
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                Yii::$app->getSession()->setFlash('error', $err);
            }
        }
        return $this->render('role-create', [
            'model' => $model,
        ]);
    }

    public function actionRoleUpdate($name)
    {
        $model = new Rbac(['scenario'=>'role']);
        $model = $model->fillModel($name);
        if( yii::$app->getRequest()->getIsPost() ) {
            if ($model->load(yii::$app->getRequest()->post()) && $model->validate() && $model->updateRole($name)) {
                yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->redirect(['roles']);
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                Yii::$app->getSession()->setFlash('error', $err);
            }
        }
        return $this->render('role-update', [
            'model' => $model
        ]);
    }

    public function actionRoleSort()
    {
        if (yii::$app->getRequest()->getIsPost()) {
            $authManager = yii::$app->getAuthManager();
            $data = yii::$app->getRequest()->post();
            if (! empty($data['sort'])) {
                foreach ($data['sort'] as $key => $value) {
                    $model = new Rbac(['scenario'=>'role']);
                    $model = $model->fillModel($key);
                    if ($model->sort != $value) {
                        $role = $authManager->getRole($key);
                        $role->data = json_encode([
                            'sort' => $value,
                        ]);
                        yii::$app->getAuthManager()->update($key, $role);
                    }
                }
            }
        }
        $this->redirect(['roles']);
    }

    public function actionRoleDelete($name='')
    {
        $authManager = yii::$app->getAuthManager();
        if ($name == '') {
            Yii::$app->getResponse()->format = Response::FORMAT_JSON;
            $id = yii::$app->getRequest()->get('id', '');
            if (! $id) {
                return ['code' => 1, 'message' => yii::t('app', "Name doesn't exit")];
            }
            $ids = explode(',', $id);
            $errorIds = [];
            $model = null;
            foreach ($ids as $one) {
                $role = $authManager->getRole($one);
                if (! $authManager->remove($role)) {
                    $errorIds[] = $one;
                }
            }
            if (count($errorIds) == 0) {
                return ['code' => 0, 'message' => yii::t('app', 'Success')];
            } else {
                return ['code' => 1, 'message' => 'id ' . implode(',', $errorIds) . yii::t('app', 'Error')];
            }
        }else {
            $role = $authManager->getRole($name);
            if ($authManager->remove($role)) {
                return ['code' => 0, 'message' => yii::t('app', 'Success')];
            } else {
                return ['code' => 1, 'message' => yii::t('app', 'Error')];
            }
        }
    }

}