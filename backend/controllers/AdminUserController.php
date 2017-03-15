<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-31 15:01
 */
namespace backend\controllers;

use yii;
use backend\models\AdminRoles;
use backend\models\User;
use yii\data\ActiveDataProvider;
use backend\models\AdminRoleUser;

class AdminUserController extends BaseController
{

    public function getIndexData()
    {
        $query = User::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_ASC,
                ]
            ]
        ]);
        return [
            'dataProvider' => $dataProvider,
        ];
    }

    public function actionCreate()
    {
        $model = new User();
        $model->setScenario('create');
        $rolesModel = new AdminRoleUser();
        if (yii::$app->getRequest()->getIsPost()) {
            if ($model->load(Yii::$app->getRequest()
                    ->post()) && $model->validate() && $rolesModel->load(yii::$app->getRequest()
                    ->post()) && $rolesModel->validate() && $model->save()
            ) {
                $rolesModel->uid = $model->getPrimaryKey();
                $rolesModel->save();
                Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->redirect(['index']);
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                Yii::$app->getSession()->setFlash('error', $err);
            }
        }
        $temp = AdminRoles::find()->asArray()->all();
        $roles = [];
        foreach ($temp as $v) {
            $roles[$v['id']] = $v['role_name'];
        }
        return $this->render('create', [
            'model' => $model,
            'rolesModel' => $rolesModel,
            'roles' => $roles
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->getModel($id);
        $model->setScenario('update');
        $rolesModel = AdminRoleUser::findOne(['uid' => $id]);
        if ($rolesModel == null) {
            $rolesModel = new AdminRoleUser();
            $rolesModel->uid = $id;
        }
        if (Yii::$app->getRequest()->getIsPost()) {
            if ($model->load(Yii::$app->request->post()) && $model->validate() && $rolesModel->load(yii::$app->getRequest()
                    ->post()) && $rolesModel->validate() && $model->save() && $rolesModel->save()
            ) {
                Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->redirect(['update', 'id' => $model->getPrimaryKey()]);
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                Yii::$app->getSession()->setFlash('error', $err);
            }
            $model = User::findOne(['id' => yii::$app->getUser()->getIdentity()->getId()]);
        }

        $temp = AdminRoles::find()->asArray()->all();
        $roles = [];
        foreach ($temp as $v) {
            $roles[$v['id']] = $v['role_name'];
        }
        return $this->render('update', [
            'model' => $model,
            'rolesModel' => $rolesModel,
            'roles' => $roles
        ]);
    }

    public function getModel($id = '')
    {
        return User::findOne(['id' => $id]);
    }

    public function actionUpdateSelf()
    {
        $model = User::findOne(['id' => yii::$app->getUser()->getIdentity()->getId()]);
        $model->setScenario('self-update');
        if (yii::$app->getRequest()->getIsPost()) {
            if ($model->validate() && $model->load(yii::$app->getRequest()->post()) && $model->self_update()) {
                Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                Yii::$app->getSession()->setFlash('error', $err);
            }
            $model = User::findOne(['id' => yii::$app->getUser()->getIdentity()->getId()]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionUpdateSelfAvatar()
    {
        $model = User::findOne(['id' => yii::$app->getUser()->getIdentity()->getId()]);
        $model->setScenario('update');
        if (yii::$app->getRequest()->getIsPost() && $model->validate() && $model->load(yii::$app->getRequest()
                ->post()) && $model->save()
        ) {
            return $this->redirect(['site/main']);
        }
        return $this->render('update-self-avatar', [
            'model' => $model,
        ]);
    }

    public function actionAssign($uid = '')
    {
        $model = AdminRoleUser::findOne(['uid' => $uid]);//->createCommand()->getRawSql();var_dump($model);die;
        if ($model == '') {//echo 11;die;
            $model = new AdminRoleUser();
        }
        $model->uid = $uid;
        if (yii::$app->getRequest()->getIsPost()) {
            if ($model->load(yii::$app->getRequest()->post()) && $model->save()) {
                Yii::$app->getSession()->setFlash('success', yii::t('app', 'success'));
            } else {//var_dump($model->getErrors());die;
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                Yii::$app->getSession()->setFlash('error', $err);
            }
        }
        $temp = AdminRoles::find()->asArray()->all();
        $roles = [];
        foreach ($temp as $v) {
            $roles[$v['id']] = $v['role_name'];
        }
        return $this->render('assign', [
            'model' => $model,
            'roles' => $roles,
        ]);
    }

}