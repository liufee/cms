<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-09-12 11:30
 */

namespace backend\controllers;

use yii;
use backend\models\search\RbacSearch;
use backend\models\form\Rbac;
use yii\web\MethodNotAllowedHttpException;
use yii\web\Response;
use yii\web\UnprocessableEntityHttpException;

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
            $data = yii::$app->getRequest()->post();
            if (! empty($data['sort'])) {
                foreach ($data['sort'] as $key => $value) {
                    $model = new Rbac(['scenario'=>'permission']);
                    $model->fillModel($key);
                    if ($model->sort != $value) {
                        $model->sort = $value;
                        $model->updatePermission($key);
                    }
                }
            }
        }
        return [];
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
        $model->fillModel($name);
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

    public function actionPermissionViewLayer($name)
    {
        $model = new Rbac(['scenario'=>'permission']);
        $model->fillModel($name);
        return $this->render('permission-view-layer', [
            'model' => $model,
        ]);
    }

    public function actionPermissionDelete($name=null)
    {
        $model = new Rbac(['scenario'=>'permission']);
        if( yii::$app->getRequest()->getIsPost() ){
            yii::$app->getResponse()->format = Response::FORMAT_JSON;
            $param = yii::$app->getRequest()->post('id', null);
            if($param !== null){
                $name = $param;
            }
            $ids = explode(',', $name);
            $errorIds = [];
            foreach ($ids as $id) {
                $model->fillModel($id);
                if (! $model->deletePermission()) {
                    $errorIds[] = $id;
                }
            }
            if (count($errorIds) == 0) {
                return ['code' => 0, 'message' => yii::t('app', 'Success')];
            } else {
                return ['code' => 1, 'message' => 'id ' . implode(',', $errorIds) . yii::t('app', 'Error')];
            }
        }else {
            throw new MethodNotAllowedHttpException(yii::t('app', "Delete must be POST http method"));
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
        $model->fillModel($name);
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

    public function actionRoleViewLayer($name)
    {
        $model = new Rbac(['scenario'=>'role']);
        $model->fillModel($name);
        return $this->render('role-view-layer', [
            'model' => $model,
        ]);
    }

    public function actionRolesSort()
    {
        if (yii::$app->getRequest()->getIsPost()) {
            $data = yii::$app->getRequest()->post();
            if (! empty($data['sort'])) {
                foreach ($data['sort'] as $key => $value) {
                    $model = new Rbac(['scenario'=>'role']);
                    $model->fillModel($key);
                    if ($model->sort != $value) {
                       $model->sort = $value;
                       $model->updateRole($key);
                    }
                }
            }
        }
        return [];
    }

    public function actionRoleDelete($name='', $id=null)
    {
        if( yii::$app->getRequest()->getIsPost() ) {
            $model = new Rbac(['scenario' => 'role']);
            if ($name == '') {
                Yii::$app->getResponse()->format = Response::FORMAT_JSON;
                $param = yii::$app->getRequest()->post('id', null);
                if($param !== null) $id = $param;
                if (!$id) {
                    return ['code' => 1, 'message' => yii::t('app', "Name doesn't exit")];
                }
                $ids = explode(',', $id);
                $errorIds = [];
                foreach ($ids as $one) {
                    $model->fillModel($one);
                    if (!$model->deleteRole()) {
                        $errorIds[] = $one;
                    }
                }
                if (count($errorIds) == 0) {
                    return [];
                } else {
                    throw new UnprocessableEntityHttpException('id ' . implode(',', $errorIds));
                }
            } else {
                $model->fillModel($name);
                if ($model->deleteRole()) {
                    if (yii::$app->getRequest()->getIsAjax()) {
                        return [];
                    } else {
                        return $this->redirect(yii::$app->request->headers['referer']);
                    }
                } else {
                    throw new UnprocessableEntityHttpException(yii::t('app', 'Error'));
                }
            }
        }else{
            throw new MethodNotAllowedHttpException(yii::t('app', "Delete must be POST http method"));
        }
    }

}