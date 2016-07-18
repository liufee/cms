<?php
//header('Access-Control-Allow-Origin: http://www.baidu.com'); //设置http://www.baidu.com允许跨域访问
//header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With'); //设置允许的跨域header
date_default_timezone_set("Asia/chongqing");
error_reporting(E_ALL);
header("Content-Type: text/html; charset=utf-8");

$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("config.json")), true);
$action = $_GET['action'];

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
if( file_exists(__DIR__ . '/../../../../../../../backend/config/main.php') ) {
    require(__DIR__ . '/../../../../../../../vendor/autoload.php');
    require(__DIR__ . '/../../../../../../../vendor/yiisoft/yii2/Yii.php');
    require(__DIR__ . '/../../../../../../../common/config/bootstrap.php');

    $config = yii\helpers\ArrayHelper::merge(
        require(__DIR__ . '/../../../../../../../common/config/main.php'),
        require(__DIR__ . '/../../../../../../../common/config/main-local.php'),
        require(__DIR__ . '/../../../../../../../backend/config/main.php'),
        require(__DIR__ . '/../../../../../../../common/config/main-local.php')
    );
    $CONFIG['baseUploadsDirectory'] = __DIR__ .'/../../../../../../../';
}else{
    require(__DIR__ . '/../../../../../../../../vendor/autoload.php');
    require(__DIR__ . '/../../../../../../../../vendor/yiisoft/yii2/Yii.php');
    require(__DIR__ . '/../../../../../../../../common/config/bootstrap.php');

    $config = yii\helpers\ArrayHelper::merge(
        require(__DIR__ . '/../../../../../../../../common/config/main.php'),
        require(__DIR__ . '/../../../../../../../../common/config/main-local.php'),
        require(__DIR__ . '/../../../../../../../../backend/config/main.php'),
        require(__DIR__ . '/../../../../../../../../common/config/main-local.php')
    );
    $CONFIG['baseUploadsDirectory'] = __DIR__ .'/../../../../../../../../';
}

$application = new yii\web\Application($config);
$CONFIG['imageUrlPrefix'] = yii::$app->params['site']['url'].'/uploads/article/ueditor';
switch ($action) {
    case 'config':
        $result =  json_encode($CONFIG);
        break;

    /* 上传图片 */
    case 'uploadimage':
    /* 上传涂鸦 */
    case 'uploadscrawl':
    /* 上传视频 */
    case 'uploadvideo':
    /* 上传文件 */
    case 'uploadfile':
        $result = include("action_upload.php");
        break;

    /* 列出图片 */
    case 'listimage':
        $result = include("action_list.php");
        break;
    /* 列出文件 */
    case 'listfile':
        $result = include("action_list.php");
        break;

    /* 抓取远程文件 */
    case 'catchimage':
        $result = include("action_crawler.php");
        break;

    default:
        $result = json_encode(array(
            'state'=> '请求地址出错'
        ));
        break;
}

/* 输出结果 */
if (isset($_GET["callback"])) {
    if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
        echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
    } else {
        echo json_encode(array(
            'state'=> 'callback参数不合法'
        ));
    }
} else {
    echo $result;
}