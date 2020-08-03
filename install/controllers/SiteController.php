<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace install\controllers;

use Yii;
use install\database\Tables;
use Exception;
use yii\db\Connection;
use yii\helpers\Url;
use common\models\AdminUser;
use common\models\Options;
use yii\web\Response;
use yii\web\ErrorAction;

/**
 * Site controller
 */
class SiteController extends \yii\web\Controller
{

    public $enableCsrfValidation = false;

    public static $installLockFile = '@install/install.lock';

    public function init()
    {
        parent::init();
        if (self::getIsInstalled()) {
            $response =  Yii::$app->getResponse();
            $response->content = Yii::t('install', "Has been installed, if you want to reinstall please remove ") . Yii::getAlias(self::$installLockFile) . yii::t('install', ' and try it again');
            $response->send();
            exit(0);
        }
    }

    public static function getIsInstalled()
    {
        return file_exists(Yii::getAlias(self::$installLockFile));
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::className(),
            ],
        ];
    }

    public function actionError()
    {
        return $this->render('error');
    }

    public function actionIndex()
    {
        return $this->redirect(['choose-language']);
    }

    public function actionChooseLanguage()
    {
        return $this->render('choose-language');
    }

    public function actionAccept()
    {
        return $this->render('accept');
    }

    public function actionCheckEnvironment()
    {
        if (!isset($_SESSION['_install_env_passed']) || $_SESSION['_install_env_passed'] != 1) {
            if (!in_array(Yii::$app->getRequest()->headers['referer'], [
                Url::to(['accept'], true),
                Url::to(['check-environment'], true)
            ])
            ) {
                return $this->redirect(['accept']);
            };
        }
        $data = array();
        $data['phpversion'] = @phpversion();
        $data['os'] = PHP_OS;
        $tmp = function_exists('gd_info') ? gd_info() : array();
        $max_execution_time = ini_get('max_execution_time');
        $allow_reference = (ini_get('allow_call_time_pass_reference') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
        $allow_url_fopen = (ini_get('allow_url_fopen') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
        $safe_mode = (ini_get('safe_mode') ? '<font color=red>[×]On</font>' : '<font color=green>[√]Off</font>');

        $err = 0;

        if (!version_compare($data['phpversion'], '5.4', '<')) {
            $data['phpversion'] = '<i class="fa fa-check correct"></i> ' . Yii::t('install', 'Yes') . ' ' . $data['phpversion'];
        } else {
            $data['phpversion'] = '<i class="fa fa-remove error"></i> ' . Yii::t('install', 'No') . ' ' . $data['phpversion'];
            $err++;
        }

        if (empty($tmp['GD Version'])) {
            $gd = '<font color=red>[×]Off</font>';
            if (!extension_loaded('imagick')){
                $err++;
            }
        } else {
            $gd = '<font color=green>[√]On</font> ' . $tmp['GD Version'];
        }

        if (class_exists('pdo')) {
            $data['pdo'] = '<i class="fa fa-check correct"></i> ' . Yii::t('install', 'Yes');
        } else {
            $data['pdo'] = '<i class="fa fa-remove error"></i> ' . Yii::t('install', 'No');
            $err++;
        }

        /*if (extension_loaded('pdo_mysql')) {
            $data['pdo_mysql'] = '<i class="fa fa-check correct"></i> ' . Yii::t('install', 'Yes');
        } else {
            $data['pdo_mysql'] = '<i class="fa fa-remove error"></i> ' . Yii::t('install', 'No');
            $err++;
        }*/

        if (extension_loaded('curl')) {
            $data['curl'] = '<i class="fa fa-check correct"></i> ' . Yii::t('install', 'Yes');
        } else {
            $data['curl'] = '<i class="fa fa-remove error"></i> ' . Yii::t('install', 'No');
            $err++;
        }

        if (extension_loaded('gd')) {
            $data['gd'] = '<i class="fa fa-check correct"></i> ' . Yii::t('install', 'Yes');
        } else {
            $data['gd'] = '<i class="fa fa-remove error"></i> ' . Yii::t('install', 'No');
            if (function_exists('imagettftext')) {
                $data['gd'] .= '<br><i class="fa fa-remove error"></i> FreeType Support ' . Yii::t('install', 'No');
            }
            $err++;
        }

        if (extension_loaded('json')) {
            $data['json'] = '<i class="fa fa-check correct"></i> ' . Yii::t('install', 'Yes');
        } else {
            $data['json'] = '<i class="fa fa-remove error"></i> ' . Yii::t('install', 'No');
            $err++;
        }

        if (extension_loaded('mbstring')) {
            $data['mbstring'] = '<i class="fa fa-check correct"></i> ' . Yii::t('install', 'Yes');
        } else {
            $data['mbstring'] = '<i class="fa fa-remove error"></i> ' . Yii::t('install', 'No');
            $err++;
        }

        if (ini_get('file_uploads')) {
            $data['upload_size'] = '<i class="fa fa-check correct"></i> ' . ini_get('upload_max_filesize');
        } else {
            $data['upload_size'] = '<i class="fa fa-remove error"></i> ' . Yii::t('install', 'Forbidden');
        }

        if (function_exists('session_start')) {
            $data['session'] = '<i class="fa fa-check correct"></i> ' . Yii::t('install', 'Yes');
        } else {
            $data['session'] = '<i class="fa fa-remove error"></i> ' . Yii::t('install', 'No');
            $err++;
        }

        $folders = array(
            '@frontend/runtime',
            '@frontend/web/assets',
            '@backend/runtime',
            '@frontend/web/admin/assets',
        );
        $newFolders = array();
        foreach ($folders as &$dir) {
            $dir = Yii::getAlias($dir);
            if (is_writable($dir)) {
                $newFolders[$dir]['w'] = true;
            } else {
                $newFolders[$dir]['w'] = false;
                $err++;
            }
            if (is_readable($dir)) {
                $newFolders[$dir]['r'] = true;
            } else {
                $newFolders[$dir]['r'] = false;
                $err++;
            }
        }
        $data['folders'] = $newFolders;
        $_SESSION['_install_env_passed'] = 0;
        if ($err == 0) {
            $_SESSION['_install_env_passed'] = 1;
        }
        $data['err'] = $err;
        return $this->render('check-environment', $data);
    }

    public function actionSetinfo()
    {
        set_time_limit(300);
        if (! isset($_SESSION['_install_env_passed']) || $_SESSION['_install_env_passed'] != 1) {
            $url = Url::to(['check-environment']);
            echo "<script>alert('" . Yii::t('install', 'Please check your environment to suite the cms') . Yii::t('install', ' If environment have been suit to the cms please check php session can set correctly') . "');location.href='{$url}';</script>";
            exit(0);
        }
        if (Yii::$app->getRequest()->getIsPost()) {
            $this->on(self::EVENT_AFTER_ACTION, function () {
                $request = Yii::$app->getRequest();
                Yii::$app->getResponse()->format = Response::FORMAT_JSON;

                try {
                    $db = $this->_getDbConnection();
                    Yii::$app->set("db", $db);

                    $tables = new Tables(['db' => Yii::$app->db]);
                    $tables->importDatabase();

                    //更新配置信息
                    $data = [
                        'username' => $request->post('manager', 'admin'),
                        'password_hash' => Yii::$app->security->generatePasswordHash($request->post('manager_pwd')),
                        'email' => $request->post('manager_email'),
                    ];
                    Yii::$app->getDb()->createCommand()->update(AdminUser::tableName(), $data, 'id = 1')->execute();

                    $model = Options::findOne(['name' => 'website_title']);
                    $model->value = $request->post('sitename', 'Feehi CMS');
                    $model->save(false);
                    $model = Options::findOne(['name' => 'website_url']);
                    $model->value = $request->post('website_url', '');
                    $model->save(false);
                    $model = Options::findOne(['name' => 'website_url']);
                    $model->value = $request->post('siteurl', '');
                    $model->save(false);
                    $model = Options::findOne(['name' => 'seo_keywords']);
                    $model->value = $request->post('sitekeywords', '');
                    $model->save(false);
                    $model = Options::findOne(['name' => 'seo_description']);
                    $model->value = $request->post('siteinfo', '');
                    $model->save(false);
                }catch (Exception $e){
                    echo str_repeat(" ", 1024 * 64 * 99);
                    $msg = str_replace(array("\r\n", "\r", "\n"), "", $e->getMessage());
                    $msg = str_replace('"', '', $msg);
                    $msg = str_replace("'", '', $msg);
                    echo "<script type=\"text/javascript\">alert(\"$msg\");history.back();</script>";
                    ob_flush();
                    flush();
                    exit(0);
                }

                $_SESSION["_install_setinfo"] = 1;
                $configFile = yii::getAlias("@common/config/main-local.php");
                $array = require $configFile;
                $array['components']['db'] = [
                    'class' => yii\db\Connection::className(),
                    'dsn' => $db->dsn,
                    'username' => $db->username,
                    'password' => $db->password,
                    'charset' => 'utf8',
                    'tablePrefix' => $db->tablePrefix,
                ];
                $str = "<?php \n return " . var_export($array,true) . ";";
                $str = str_replace('\\\\', '\\', $str);
                if( !file_put_contents($configFile, $str) ){
                    sleep(1);
                    $message = Yii::t("install", "Installed success;but update write config file error.please update common/config/main-local.php components db section.");
                    echo "<script>alert('{$message}');location.href='" . Url::to(['success']) . "';</script>";
                }else {
                    sleep(1);
                    echo "<script>location.href='" . Url::to(['success']) . "';</script>";
                }
                exit(0);
            });
            $html = $this->render('installing');
            echo $html;
            flush();
            if(ob_get_level() > 0){
                ob_flush();
            }
        } else {
            return $this->render('setinfo');
        }
    }

    public function actionCreateDatabase()
    {
        Yii::$app->getResponse()->format = Response::FORMAT_JSON;

        try {
            $this->_getDbConnection();
            return ['message' => ''];
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function _getDbConnection()
    {
        $request = Yii::$app->getRequest();
        $dbtype = $request->post('dbtype', 'mysql');
        $dbhost = $request->post('dbhost', 'dbhost');
        $dbuser = $request->post('dbuser', 'root');
        $dbpassword = $request->post('dbpw', '');
        $dbport = $request->post('dbport', '3306');
        $dbname = $request->post('dbname', '');
        $tablePrefix = $request->post("dbprefix", '');
        $dsn = '';
        switch ($dbtype) {
            case "pgsql":
            case "mysql":
                $dsn = $dbtype . ":host=" . $dbhost . ';port=' . $dbport;
                if( !empty($dbname) ) $dsn .= ";dbname=" . $dbname;
                break;

            case "sqlite":
                $path = Yii::getAlias($dbhost);
                $dsn = "sqlite:$path";
                break;
        }
        $db = new Connection([
            'dsn' => $dsn,
            'username' => $dbuser,
            'password' => $dbpassword,
            'charset' => 'utf8',
            'tablePrefix' => $tablePrefix,
        ]);

        $this->checkAccountPermission($db, $dbtype, $dbname);

        return $db;
    }

    public function actionSuccess()
    {
        if (isset($_SESSION["_install_setinfo"]) && $_SESSION["_install_setinfo"] == 1) {
            if( !touch(Yii::getAlias(self::$installLockFile)) ){
                $message = Yii::t("install", "Touch install lock file " . self::$installLockFile . " failed,please touch file handled" );
                echo "<script>alert('{$message}')</script>";
            }
            session_destroy();
            return $this->render("success");
        } else {
            return $this->redirect(['setinfo']);
        }

    }

    public function actionLanguage()
    {
        $language = Yii::$app->getRequest()->get('lang');//echo $language;die;
        if (isset($language)) {
            Yii::$app->session['language'] = $language;
        }
        return $this->redirect(['accept']);
    }

    private function checkAccountPermission(Connection $db, $dbtype, $dbname)
    {
        if($dbtype != "sqlite") {
            try {
                if($dbtype === "pgsql"){
                }else {
                    $db->createCommand("use $dbname")->execute();
                }
            }catch (\yii\db\Exception $e){
                if( $e->getCode() == 1049 ) {
                    $result = $db->createCommand("CREATE DATABASE IF NOT EXISTS `{$dbname}` DEFAULT CHARACTER SET utf8")->execute();
                    if ($result == 1) {
                        $this->checkAccountPermission($db, $dbtype, $dbname);
                        return;
                    } else {
                        throw new Exception(Yii::t('install', 'Create database error, please create yourself and retry'));
                    }
                }else{
                    throw new Exception($e->getMessage());
                }
            }
            $db->createCommand("create table test(id integer)")->execute();
            $db->createCommand("insert into test values(1)")->execute();
            $result = $db->createCommand("select * from test where id=1")->queryOne();
            if ($result === false) {
                throw new Exception(Yii::t('install', 'Access to database `{database}` error. Maybe permission denied', ['database' => $dbname]));
            }
            try {
                $db->createCommand("use $dbname")->execute();
                $db->createCommand("drop table test")->execute();
            } catch (Exception $exception) {
                Yii::error("after install feehicms delete test database table `test` error");
            }
        }

    }
}