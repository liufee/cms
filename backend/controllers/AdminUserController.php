<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-31 15:01
 */

namespace backend\controllers;

use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use common\services\RBACService;
use common\services\RBACServiceInterface;
use Yii;
use common\models\AdminUser;
use common\services\AdminUserServiceInterface;
use backend\models\form\PasswordResetRequestForm;
use backend\models\form\ResetPasswordForm;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use backend\actions\ViewAction;

/**
 * AdminUser management
 * - data:
 *          table admin_user
 *
 * Class AdminUserController
 * @package backend\controllers
 */
class AdminUserController extends \yii\web\Controller
{
    /**
     * @auth
     * - item group=权限 category=管理员 description-get=列表 sort=520 method=get
     * - item group=权限 category=管理员 description-get=查看 sort=521 method=get  
     * - item group=权限 category=管理员 description-post=删除 sort=522 method=post  
     * - item group=权限 category=管理员 description-post=排序 sort=523 method=post 
     * - item group=权限 category=管理员 description=创建 sort-get=524 sort-post=525 method=get,post
     * - item group=权限 category=管理员 description=修改 sort-get=526 sort-post=527 method=get,post
     * - item rbac=false
     *  
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actions()
    {
        /** @var AdminUserServiceInterface $service */
        $service = Yii::$app->get(AdminUserServiceInterface::ServiceName);
        /** @var RBACServiceInterface $rbacService */
        $rbacService = Yii::$app->get(RBACServiceInterface::ServiceName);
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function($query)use($service){
                    $result = $service->getList($query);
                    return [
                        'dataProvider' => $result['dataProvider'],
                        'searchModel' => $result['searchModel'],
                    ];
                }
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'data' => function($id)use($service){
                    return [
                        'model' => $service->getDetail($id),
                    ];
                },
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'delete' => function($id) use($service){
                    return $service->delete($id);
                },
            ],
            'sort' => [
                'class' => SortAction::className(),
                'sort' => function($id, $sort) use($service){
                    return $service->sort($id, $sort);
                },
            ],
            'create' => [
                'class' => CreateAction::className(),
                'create' => function($postData) use($service, $rbacService){
                    /** @var AdminUser $model */
                    return $service->create($postData);
                },
                'data' => function()use($service, $rbacService){
                    return [
                        'model' => $service->newModel(),
                        'assignModel' => $rbacService->newAssignPermissionModel(),
                        'permissions' => $rbacService->getPermissionsGroups(),
                        'roles' => $rbacService->getRoles(),
                    ];
                }
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'update' => function($id, $postData) use($service){
                    return $service->update($id, $postData);
                },
                'data' => function($id, $updateResultModel)use($service, $rbacService){
                    return [
                        'model' => $updateResultModel === null ? $service->getDetail($id, ['scenario'=>"update"]) : $updateResultModel,
                        'assignModel' => $rbacService->getAssignPermissionDetail($id),
                        'permissions' => $rbacService->getPermissionsGroups(),
                        'roles' => $rbacService->getRoles(),
                    ];
                }
            ],
            'update-self' => [
                'class' => UpdateAction::className(),
                'update' => function($id, $postData) use($service){
                    return $service->updateSelf($id, $postData, ['scenario'=>'self-update']);
                },
                'data' => function($id, $updateResultModel) use($service){
                    return [
                        'model' => $updateResultModel === null ? $service->getDetail($id) : $updateResultModel,
                    ];
                },
                'viewFile' => 'update',
            ]
        ];
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