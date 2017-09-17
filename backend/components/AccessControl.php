<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-09-10 16:42
 */

namespace backend\components;

use yii\web\ForbiddenHttpException;
use yii\base\Module;
use Yii;
use yii\web\User;
use yii\di\Instance;

class AccessControl extends \yii\base\ActionFilter
{
    /* @var User */
    private $_user = 'user';

    public $allowActions = [];

    public $superAdminUserIds = [];

    /**
     * Get user
     * @return User
     */
    public function getUser()
    {
        if (!$this->_user instanceof User) {
            $this->_user = Instance::ensure($this->_user, User::className());
        }
        return $this->_user;
    }

    /**
     * Set user
     * @param User|string $user
     */
    public function setUser($user)
    {
        $this->_user = $user;
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $actionId = $action->getUniqueId();
        $user = $this->getUser();
        if( in_array($user->getId(), $this->superAdminUserIds) ){
            return true;
        }
        if (self::checkRoute('/' . $actionId, Yii::$app->getRequest()->get(), $user)) {
            return true;
        }
        $this->denyAccess($user);
    }

    /**
     * Denies the access of the user.
     * The default implementation will redirect the user to the login page if he is a guest;
     * if the user is already logged, a 403 HTTP exception will be thrown.
     * @param  User $user the current user
     * @throws ForbiddenHttpException if the user is already logged in.
     */
    protected function denyAccess($user)
    {
        if ($user->getIsGuest()) {
            $user->loginRequired();
        } else {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
    }

    /**
     * @inheritdoc
     */
    protected function isActive($action)
    {
        $uniqueId = $action->getUniqueId();
        if ($uniqueId === Yii::$app->getErrorHandler()->errorAction) {
            return false;
        }

        $user = $this->getUser();
        if($user->getIsGuest())
        {
            $loginUrl = null;
            if(is_array($user->loginUrl) && isset($user->loginUrl[0])){
                $loginUrl = $user->loginUrl[0];
            }else if(is_string($user->loginUrl)){
                $loginUrl = $user->loginUrl;
            }
            if(!is_null($loginUrl) && trim($loginUrl,'/') === $uniqueId)
            {
                return false;
            }
        }

        if ($this->owner instanceof Module) {
            // convert action uniqueId into an ID relative to the module
            $mid = $this->owner->getUniqueId();
            $id = $uniqueId;
            if ($mid !== '' && strpos($id, $mid . '/') === 0) {
                $id = substr($id, strlen($mid) + 1);
            }
        } else {
            $id = $action->id;
        }

        foreach ($this->allowActions as $route) {
            if (substr($route, -1) === '*') {
                $route = rtrim($route, "*");
                if ($route === '' || strpos($id, $route) === 0) {
                    return false;
                }
            } else {
                if ($id === $route) {
                    return false;
                }
            }
        }

        if ($action->controller->hasMethod('allowAction') && in_array($action->id, $action->controller->allowAction())) {
            return false;
        }

        return true;
    }

    /**
     * Check access route for user.
     * @param string|array $route
     * @param integer|User $user
     * @return boolean
     */
    public static function checkRoute($route, $params = [], $user = null)
    {
        $r = static::normalizeRoute($route);

        if ($user === null) {
            $user = Yii::$app->getUser();
        }
        $userId = $user instanceof User ? $user->getId() : $user;


        if ($user->can($r, $params)) {
            return true;
        }
        while (($pos = strrpos($r, '/')) > 0) {
            $r = substr($r, 0, $pos);
            if ($user->can($r . '/*', $params)) {
                return true;
            }
        }
        return $user->can('/*', $params);

    }

    protected static function normalizeRoute($route)
    {
        if ($route === '') {
            return '/' . Yii::$app->controller->getRoute() . ':' . yii::$app->getRequest()->getMethod();
        } elseif (strncmp($route, '/', 1) === 0) {
            return $route . ':' . yii::$app->getRequest()->getMethod();
        } elseif (strpos($route, '/') === false) {
            return '/' . Yii::$app->controller->getUniqueId() . '/' . $route . ':' . yii::$app->getRequest()->getMethod();
        } elseif (($mid = Yii::$app->controller->module->getUniqueId()) !== '') {
            return '/' . $mid . '/' . $route . ':' . yii::$app->getRequest()->getMethod();
        }
        return '/' . $route . ':' . yii::$app->getRequest()->getMethod();
    }
}
