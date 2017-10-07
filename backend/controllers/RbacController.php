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

    public function actionPermissionDelete($name='')
    {
        $model = new Rbac(['scenario'=>'permission']);
        if( yii::$app->getRequest()->getIsAjax() ){
            yii::$app->getResponse()->format = Response::FORMAT_JSON;
            $id = yii::$app->getRequest()->get('name', '');
            $ids = explode(',', $id);
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
            $model->fillModel($name);
            if ($model->deletePermission() ) {
                yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
            } else {
                yii::$app->getSession()->setFlash('error', yii::t('app', 'Error'));
            }
            return $this->redirect('permissions');
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

    public function actionRoleSort()
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
        $this->redirect(['roles']);
    }

    public function actionRoleDelete($name='')
    {
        $model = new Rbac(['scenario'=>'role']);
        if ($name == '') {
            Yii::$app->getResponse()->format = Response::FORMAT_JSON;
            $id = yii::$app->getRequest()->get('id', '');
            if (! $id) {
                return ['code' => 1, 'message' => yii::t('app', "Name doesn't exit")];
            }
            $ids = explode(',', $id);
            $errorIds = [];
            foreach ($ids as $one) {
                $model->fillModel($one);
                if (! $model->deleteRole()) {
                    $errorIds[] = $one;
                }
            }
            if (count($errorIds) == 0) {
                return [];
            } else {
                throw new UnprocessableEntityHttpException('id ' . implode(',', $errorIds));
            }
        }else {
            $model->fillModel($name);
            if ($model->deleteRole()) {
                if (yii::$app->getRequest()->getIsAjax()) {
                    return [];
                }else{
                    return $this->redirect(yii::$app->request->headers['referer']);
                }
            } else {
                throw new UnprocessableEntityHttpException(yii::t('app', 'Error'));
            }
        }
    }

}