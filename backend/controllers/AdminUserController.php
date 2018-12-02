<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-31 15:01
 */

namespace backend\controllers;

use Yii;
use backend\models\form\PasswordResetRequestForm;
use backend\models\form\ResetPasswordForm;
use backend\models\User;
use backend\models\search\UserSearch;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use backend\actions\ViewAction;

class AdminUserController extends \yii\web\Controller
{
    /**
     * @auth
     * - item group=权限 category=管理员 description-get=列表 sort=520 method=get
     * - item group=权限 category=管理员 description-get=查看 sort=521 method=get  
     * - item group=权限 category=管理员 description-post=删除 sort=522 method=post  
     * - item group=权限 category=管理员 description-post=排序 sort=523 method=post  
     * @return array
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    /** @var UserSearch $searchModel */
                    $searchModel = Yii::createObject( UserSearch::className() );
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                    ];
                }
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => User::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => User::className(),
            ],
            'sort' => [
                'class' => SortAction::className(),
                'modelClass' => User::className(),
            ],
        ];
    }

    /**
     * 创建管理员账号
     *
     * @auth - item group=权限 category=管理员 description=创建 sort-get=524 sort-post=525 method=get,post
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        /** @var User $model */
        $model = Yii::createObject( User::className() );
        $model->setScenario('create');
        if (Yii::$app->getRequest()->getIsPost()) {
            if ( $model->load(Yii::$app->getRequest()->post()) && $model->save() && $model->assignPermission() ) {
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Success'));
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
        $model->loadDefaultValues();
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * 修改管理员账号
     *
     * @auth - item group=权限 category=管理员 description=修改 sort-get=526 sort-post=527 method=get,post
     * @param $id
     * @return string|\yii\web\Response
     * @throws \Throwable
     */
    public function actionUpdate($id)
    {
        $model = User::findOne($id);
        $model->setScenario('update');
        $model->roles = $model->permissions = call_user_func(function() use($id){
            $permissions = Yii::$app->getAuthManager()->getAssignments($id);
            foreach ($permissions as $k => &$v){
                $v = $k;
            }
            return $permissions;
        });
        if (Yii::$app->getRequest()->getIsPost()) {
            if ($model->load(Yii::$app->request->post()) && $model->save() && $model->assignPermission() ) {
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Success'));
                return $this->redirect(['update', 'id' => $model->getPrimaryKey()]);
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                Yii::$app->getSession()->setFlash('error', $err);
            }
            $model = User::findOne(['id' => Yii::$app->getUser()->getIdentity()->getId()]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * 管理员修改自己
     *
     * @auth - item rbac=false
     * @return string
     * @throws \Throwable
     */
    public function actionUpdateSelf()
    {
        $model = User::findOne(['id' => Yii::$app->getUser()->getIdentity()->getId()]);
        $model->setScenario('self-update');
        if (Yii::$app->getRequest()->getIsPost()) {
            if ($model->load(Yii::$app->getRequest()->post()) && $model->selfUpdate()) {
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Success'));
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                Yii::$app->getSession()->setFlash('error', $err);
            }
            $model = User::findOne(['id' => Yii::$app->getUser()->getIdentity()->getId()]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }


    /**
     * 找回密码
     *
     * @auth - item rbac=false
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionRequestPasswordReset()
    {
        $model = Yii::createObject( PasswordResetRequestForm::className() );
        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()
                    ->setFlash('success', Yii::t('app', 'Check your email for further instructions.'));

                return $this->goHome();
            } else {
                Yii::$app->getSession()
                    ->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * 管理员重置密码
     *
     * @auth - item rbac=false
     * @param $token
     * @return string|\yii\web\Response
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'New password was saved.'));

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

}