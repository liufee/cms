<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2018-11-26 12:43
 */
namespace console\controllers\helpers;

use Yii;
use ReflectionClass;
use yii\helpers\FileHelper;

class BackendAuth extends \yii\base\BaseObject
{

    /** @var string 后台控制器目录 */
    private $_controllerPath = "@backend/controllers";

    /** @var array 不需要rbac的控制器 */
    public $_noNeedRbacControllers = ['AssetsController', 'SiteController'];

    /** @var array 不需要rbac的路由 */
    private $_noNeedRbacRoutes = [];

    private $_unPropertyDocCommentsRoutes = [];

    private $_authItems = [];

    private $_dbAuthItems = null;

    public $error = null;

    public function init()
    {
        parent::init();
        $this->parseAuthItems();
    }


    public function setControllerPath($controllerPath)
    {
        $this->_controllerPath = $controllerPath;
    }

    public function getControllerPath()
    {
        return Yii::getAlias($this->_controllerPath);
    }

    public function setNoNeedRbacControllers(array $noNeedRbacControllers)
    {
        $this->_noNeedRbacControllers = $noNeedRbacControllers;
    }

    public function getNoNeedRbacControllers()
    {
        return $this->_noNeedRbacControllers;
    }

    public function setNoNeedRbacRoutes($routes)
    {
        $this->_noNeedRbacRoutes = $routes;
    }

    public function getNoNeedRbacRoutes()
    {
        return $this->_noNeedRbacRoutes;
    }

    public function getAuthItems()
    {
        return $this->_authItems;
    }

    public function getDbAuthItems()
    {
        if( $this->_dbAuthItems === null ) {
            $dbAuthItems = Yii::$app->getAuthManager()->getPermissions();
            $this->_dbAuthItems = $dbAuthItems;
        }
        return $this->_dbAuthItems;
    }

    public function setError($error){
        $this->error = $error;
    }

    public function getError()
    {
        return $this->error;
    }

    public function parseControllerPartialUrl($subDirControllerName)
    {
        $subDirControllerName = explode('/', $subDirControllerName);
        $controllerName = str_replace('Controller', '', array_pop($subDirControllerName));
        $controllerTemp = lcfirst($controllerName);
        $controllerString = "";
        for ($i = 0; $i < strlen($controllerTemp); $i++) {

            $str = ord($controllerTemp[$i]);
            if ($str > 64 && $str < 91) {
                $controllerString .= '-' . strtolower($controllerTemp[$i]);
            } else {
                $controllerString .= $controllerTemp[$i];
            }
        }

        $subDirControllerName[] = $controllerString;

        return implode('/', $subDirControllerName);
    }

    public function getActionAuthItemsByDocComment($docComments)
    {
        if( strpos( $docComments, "@auth - item" ) !== false ){
            $temp = explode("@auth", $docComments)[1];
        }else if( strpos( $docComments, "@auth" ) !== false ){
            $temp = explode("@auth", $docComments)[1];
        }else{
            return false;
        }
        if( strpos($temp, '@') !== false ){
            $temp = explode('@', $temp)[0];
            $temp = trim($temp);
            $temp = trim($temp, "*");
            $temp = trim($temp);
        }else{
            $temp = explode('*/', $temp)[0];
            $temp = trim($temp);
        }
        $arr = explode("\n", $temp);
        $authItems = [];
        foreach ($arr as $s) {
            if (strpos($s, '- item') === false) {
                continue;
            };
            $s = explode('- item', $s)[1];
            $s = trim($s);
            $row = explode(" ", $s);
            $authItem = [];
            foreach ($row as $v) {
                $t = explode("=", $v);
                if (isset($t[1])) {
                    $authItem[$t[0]] = $t[1];
                }
            }
            if( isset( $authItem['rbac'] ) && ($authItem['rbac'] == 'false' || $authItem['rbac'] == 'no' ) ){
                $authItems[] = $authItem;
            }else {
                $httpMethods = [];
                if( strpos($authItem['method'], ',') !== false ) {
                    $httpMethods = explode(',', $authItem['method']);
                    foreach ($httpMethods as $key => $httpMethod){
                        $httpMethod = trim($httpMethod);
                        $httpMethods[$key] = $this->filterAlphabetToLower($httpMethod);
                    }
                }else{
                    $authItem['method'] = trim($authItem['method']);
                    $httpMethod = $this->filterAlphabetToLower($authItem['method']);
                    $httpMethods[] = $httpMethod;
                }
                $authItem['methods'] = $httpMethods;
                unset($authItem['method']);
                $authItems[] = $authItem;
            }
        }
        return $authItems;
    }

    public function parseActionPartialUrl($actionName)
    {
        $actionTemp = lcfirst($actionName);
        $actionString = "";
        for ($i = 0; $i < strlen($actionTemp); $i++) {

            $str = ord($actionTemp[$i]);
            if ($str > 64 && $str < 91) {
                $actionString .= '-' . strtolower($actionTemp[$i]);
            } else {
                $actionString .= $actionTemp[$i];
            }
        }
        return $actionString;
    }

    public function parseAuthItems()
    {
        $controllerPath = $this->getControllerPath();

        $files = [];
        foreach (FileHelper::findFiles($controllerPath) as $file) {
            $files[] = str_replace($controllerPath . DIRECTORY_SEPARATOR, '', $file);
        }

        foreach($files as $file ) {
            if( !strpos($file, "Controller") ) continue;
            $subDirControllerName = str_replace('.php', '', $file);
            if (in_array($subDirControllerName, $this->getNoNeedRbacControllers())) {
                Yii::info($subDirControllerName . "不受权限控制,跳过");
                continue;
            }
            $class = new ReflectionClass("\\backend\\controllers\\" . str_replace('/', '\\', $subDirControllerName));
            $controllerPartialUrl = $this->parseControllerPartialUrl($subDirControllerName);
            $methods = $class->getMethods();
            foreach ($methods as $method) {
                $actions = [];
                $authItems = [];
                $actionNum = 0;
                if ($method->getName() === 'actions') {
                    $obj = $class->newInstanceArgs([1, 2]);
                    $actions = $obj->actions();
                    if( empty($actions) ) continue;
                    $authItems = $this->getActionAuthItemsByDocComment($method->getDocComment());
                    $actionNum = count($actions);
                } else if (strpos($method->getName(), 'action') === 0) {
                    $action = str_replace('action', '', $method->getName());
                    $authItems = $this->getActionAuthItemsByDocComment($method->getDocComment());
                    $actions[$action] = $action;
                    $actionNum = 1;
                }

                if (!is_array($authItems) || count($authItems) !== $actionNum) {
                    continue;
                }

                if ( count($authItems) !== $actionNum ) {
                    $this->_unPropertyDocCommentsRoutes[] = $controllerPartialUrl;
                    $error = "$subDirControllerName::actions或actionX 注释和action数量不匹配 注释(doc comment)数量" . count($authItems) . " 方法(action method)数量" . count($actions) . "(actions or actionX doc comment not equal action method quantity)";
                    $this->setError($error);
                    Yii::error($error);
                    Yii::$app->controller->stderr($error . PHP_EOL);
                    return false;
                }
                $j = 0;
                foreach ($actions as $action => $none) {
                    $actionPartialUrl = $this->parseActionPartialUrl($action);
                    $url = '/' . $controllerPartialUrl . '/' . $actionPartialUrl;
                    if( isset( $authItems[$j]['rbac'] ) && ( in_array($authItems[$j]['rbac'], ['false', 'no']) ) ){
                        $this->_noNeedRbacRoutes[] = $url;
                        continue;
                    }

                    foreach ($authItems[$j]['methods'] as $httpMethod) {
                        $httpMethod = strtoupper(trim($httpMethod));
                        $description = call_user_func(function()use($httpMethod, $authItems, $j){
                            if( isset($authItems[$j]['description-' . strtolower($httpMethod)]) ){
                                return $authItems[$j]['description-' . strtolower($httpMethod)];
                            }
                            switch ($httpMethod){
                                case 'GET':
                                    return $authItems[$j]['description'] . '(查看)';
                                case 'POST':
                                    return $authItems[$j]['description'] . '(确定)';
                            }
                        });
                        $sort = call_user_func(function()use($httpMethod, $authItems, $j){
                            if( isset($authItems[$j]['sort-' . strtolower($httpMethod)]) ){
                                return $authItems[$j]['sort-' . strtolower($httpMethod)];
                            }
                            $sort = isset( $authItems[$j]['sort'] ) ? $authItems[$j]['sort'] : 0;
                            return $sort;
                        });
                        $this->_authItems = array_merge($this->_authItems, [[
                            'name' => $url . ':' . $httpMethod,
                            'route' => $url,
                            'description' => $description,
                            'group' => $authItems[$j]['group'],
                            'category' => $authItems[$j]['category'],
                            'method' => $httpMethod,
                            'sort' => $sort,
                        ]]);
                    }
                    $j++;
                }
            }
        }
    }

    private function filterAlphabetToLower($str)
    {
        $alphabet = "";
        for ($i = 0; $i < strlen($str); $i++) {
            $n = ord($str[$i]);
            if ($n > 64 && $n < 123) {
                $alphabet .= strtolower($str[$i]);
            }
        }
        return $alphabet;
    }
}