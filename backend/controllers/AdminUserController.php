<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-31 15:01
 */

namespace backend\controllers;

use Yii;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use common\services\RBACServiceInterface;
use common\models\AdminUser;
use common\services\AdminUserServiceInterface;
use backend\models\form\PasswordResetRequestForm;
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
     * - item rbac=false
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
                        'model' => $service->newModel(['scenario' => AdminUserServiceInterface::scenarioCreate]),
                        'assignModel' => $rbacService->newAssignPermissionModel(),
                        'permissions' => $rbacService->getPermissionsGroups(),
                        'roles' => $rbacService->getRoles(),
                    ];
                }
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'update' => function($id, $postData) use($service){
                    return $service->update($id, $postData, ['scenario' => AdminUserServiceInterface::scenarioUpdate]);
                },
                'data' => function($id, $updateResultModel)use($service, $rbacService){
                    return [
                        'model' => $updateResultModel === null ? $service->getDetail($id, ['scenario' => AdminUserServiceInterface::scenarioUpdate]) : $updateResultModel,
                        'assignModel' => $rbacService->getAssignPermissionDetail($id),
                        'permissions' => $rbacService->getPermissionsGroups(),
                        'roles' => $rbacService->getRoles(),
                    ];
                }
            ],
            'self-update' => [
                'class' => UpdateAction::className(),
                'update' => function($id, $postData) use($service){
                    return $service->selfUpdate($id, $postData, ['scenario' => AdminUserServiceInterface::scenarioSelfUpdate]);
                },
                'data' => function($id, $updateResultModel) use($service){
                    return [
                        'model' => $updateResultModel === null ? $service->getDetail($id, ['scenario' => AdminUserServiceInterface::scenarioSelfUpdate]) : $updateResultModel,
                    ];
                },
                'viewFile' => 'update',
            ],
            'request-password-reset' => [
                'class' => UpdateAction::className(),
                'primaryKeyIdentity' => null,
                'update' => function($postData) use($service){
                    $result = $service->sendResetPasswordLink($postData);
                    if( $result === false ){
                        return 'Sorry, we are unable to reset password for email provided.';
                    }
                    //@todo if success tips 'Check your email for further instructions.'
                    return $result;
                },
                'data' => function($updateResultModel){
                    return [
                        "model" => $updateResultModel === null ? new PasswordResetRequestForm() : $updateResultModel,
                    ];
                },
                'viewFile' => 'requestPasswordResetToken',
            ],
            'reset-password' => [
                'class' => UpdateAction::className(),
                'primaryKeyIdentity' => 'token',
                'update' => function($token, $postData) use($service) {
                    return $service->resetPassword($token, $postData);
                },
                'data' => function($token, $updateResultModel) use($service) {
                    if( $updateResultModel === null ){
                        try {
                            $model = $service->newResetPasswordForm($token);
                        }catch (InvalidParamException $e) {
                                throw new BadRequestHttpException($e->getMessage());
                        }
                    }else{
                        $model = $updateResultModel;
                    }
                    return [
                        'model' => $model,
                    ];
                },
                'successRedirect' => $this->getHomeUrl(),//@todo tips 'New password was saved.'
                'viewFile' => 'resetPassword'
            ]
        ];
    }

    private function getHomeUrl()
    {
        if( Yii::$app->getRequest()->getIsConsoleRequest() ){//when execute ./yii feehi/permission
            return "/";
        }
        return Yii::$app->getHomeUrl();
    }
}