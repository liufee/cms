<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2018-05-21 20:09
 */

namespace console\controllers;

use Yii;
use FilesystemIterator;
use common\helpers\Util;
use common\models\Article;
use common\models\ArticleContent;
use common\models\FriendlyLink;
use backend\models\form\RBACPermissionForm;
use common\services\RBACServiceInterface;
use console\helpers\FileHelper;
use console\controllers\helpers\BackendAuth;
use yii\base\Exception;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\imagine\Image;
use yii\web\Request;

class FeehiController extends Controller
{

    /**
     * @var string Directory where you want to generate RBAC Permission rules, often is backend/controllers
     */
    public $generateRBACControllerPath = "@backend/controllers";

    /**
     * @var array a list of $generateRBACControllerPath directory that to need generate controllers
     */
    public $noNeedRBACControllers = ['AssetsController', 'SiteController'];

    /**
     * Download FeehiCMS demo articles pics, videos.
     *
     * Execute `/path/to/yii feehi/download-upload-files` will download demo articles attachments(picture...)
     *
     * Do not forget config the correct frontend uri at ip:port/admin/index.php?r=setting/website
     */
    public function actionDownloadUploadFiles()
    {
        ini_set('memory_limit','1024M');
        ini_set('default_socket_timeout', 1);
        $uploadsZipUrl = "http://resource-1251086492.cossh.myqcloud.com/cms/uploads.zip";
        $zipPhpUrl = "http://resource-1251086492.cossh.myqcloud.com/cms/pclzip.lib.php";
        $runtime = Yii::getAlias("@frontend/runtime/");
        $this->stdout("downloading uploads.zip hold on please..." . PHP_EOL);
        $uploadsZip = $runtime . "uploads.zip";
        list($bin, $err) = FileHelper::request($uploadsZipUrl);
        if($err !== ""){
            $this->stdout("download uploads.zip failed " . $err . " please download yourself " . $uploadsZipUrl . PHP_EOL, Console::BG_RED);
            return 1;
        }
        $saveSuccess = file_put_contents($uploadsZip, $bin);
        if(!$saveSuccess) {
            $this->stdout("download uploads.zip success, save to hard disk failed please download yourself " . $uploadsZipUrl . PHP_EOL, Console::BG_RED);
            return 1;
        }
        $this->stdout("download uploads.zip finished" . PHP_EOL);
        $this->stdout("unzip uploads.zip hold on please..." . PHP_EOL);
        if( extension_loaded("zip") ){
            FileHelper::unzip($uploadsZip, Yii::getAlias("@frontend/web/uploads/"));
        }else {
            $this->stdout("prepare unzip environment..." . PHP_EOL);
            $zipphp = file_get_contents($zipPhpUrl);
            file_put_contents($runtime . 'zip.php', $zipphp);
            if (md5_file($runtime . 'zip.php') != "2334984a0c7c8bd0a05e3cea80b60aae") {
                $this->stdout("signature check failed, please download uploads.zip yourself " . $uploadsZipUrl . PHP_EOL, Console::BG_RED);
                @unlink($runtime . 'zip.php');
                return 1;
            }
            require_once $runtime . 'zip.php';

            $archive = new \PclZip($uploadsZip);
            if ($archive->extract(PCLZIP_OPT_PATH, Yii::getAlias("@frontend/web/uploads"), PCLZIP_OPT_REMOVE_PATH, 'install/release') == 0) {
                $this->stdout("get files failed，please download uploads.zip and place to frontend/web/uploads" . PHP_EOL);
            }
            @unlink($runtime . 'zip.php');
        }
        @unlink($uploadsZip);
        $this->stdout("unzip to " . Yii::getAlias("@frontend/web/uploads/") . "success" . PHP_EOL, Console::FG_GREEN);
    }

    /**
     * Generate RBAC permission items
     *
     * more info please visit http://doc.feehi.com/rbac.html
     */
    public function actionPermission()
    {
        Yii::$app->set("request", new Request());
        /** @var BackendAuth $backendAuth */
        $backendAuth = Yii::createObject([
            'class' => BackendAuth::className(),
        ]);
        $backendAuth->setControllerPath($this->generateRBACControllerPath);
        $backendAuth->setNoNeedRBACControllers($this->noNeedRBACControllers);

        /** @var RBACServiceInterface $service */
        $service = Yii::$app->get(RBACServiceInterface::ServiceName);
        $authItems = $backendAuth->getAuthItems();
        $dbAuthItems = $backendAuth->getDbAuthItems();

        $needModifies = [];
        $needAdds = [];
        foreach ($authItems as $authItem){
            if( isset( $dbAuthItems[$authItem['name']] ) ){

                $data = json_decode($dbAuthItems[$authItem['name']]->data, true);

                if( !(
                    $authItem['name'] === $dbAuthItems[$authItem['name']]->name
                    && $authItem['description'] === $dbAuthItems[$authItem['name']]->description
                    && $authItem['group'] === $data['group']
                    && $authItem['category'] === $data['category']
                    && $authItem['sort'] === $data['sort']
                ) ){
                    $needModifies[] = $authItem;
                }
            }else{
                $needAdds[] = $authItem;
            }
        }

        $needRemoves = array_diff(array_keys($dbAuthItems) ,ArrayHelper::getColumn($authItems, 'name'));

        if( !empty($needAdds) ) {
            if (
                !$this->confirm("确定要增加下面这些规则吗?(surely add rules below?)" . PHP_EOL . implode(PHP_EOL, ArrayHelper::getColumn($needAdds, 'name')) . PHP_EOL, false)
            ) {
                $this->stdout("已取消增加(been canceled add)" . PHP_EOL, Console::FG_GREEN);
            } else {
                foreach ($needAdds as $k => $v) {
                    $model = new RBACPermissionForm();
                    $model->route = $v['route'];
                    $model->method = $v['method'];
                    $model->description = $v['description'];
                    $model->group = $v['group'];
                    $model->category = $v['category'];
                    $model->sort = $v['sort'];
                    $exits = Yii::$app->getAuthManager()->getPermission($model->route . ':' . $model->method);
                    if (!$exits) {
                        $postData = ["RBACPermissionForm" => $model->getAttributes()];
                        $result=$service->createPermission($postData);
                        if( $result !== true ){
                            throw new Exception("save permission error" . print_r($result, true));
                        }
                    }
                    $service->updatePermission($model->getName(), $model->getAttributes());
                }
            }
        }

        if( !empty($needModifies) ) {

            if (
                !$this->confirm("确定要修改下面这些规则吗?(surely update rules below?)" . PHP_EOL . implode(PHP_EOL, ArrayHelper::getColumn($needModifies, 'name')) . PHP_EOL, false)
            ) {
                $this->stdout("已取消修改(been canceled update)" . PHP_EOL, Console::FG_GREEN);
            } else {
                foreach ($needModifies as $k => $v) {
                    $model = new RBACPermissionForm();
                    $model->route = $v['route'];
                    $model->method = $v['method'];
                    $model->description = $v['description'];
                    $model->group = $v['group'];
                    $model->category = $v['category'];
                    $model->sort = $v['sort'];
                    $postData = ["RBACPermissionForm" => $model->getAttributes()];
                    $result = $service->updatePermission($model->getName(), $postData);
                    if( $result !== true ){
                        throw new Exception("update permission error " . print_r($result, true));
                    }                }
            }
        }

        if( !empty($needRemoves) ) {
            if (
                !$this->confirm("确定要删除下面这些规则吗?(surely delete rules below?)" . PHP_EOL . implode(PHP_EOL, $needRemoves) . PHP_EOL, false)
            ) {
                $this->stdout("已取消删除(been canceled delete)" . PHP_EOL, Console::FG_GREEN);
            } else {
                foreach ($needRemoves as $k => $v) {
                    $service->deletePermission($v);
                }
            }
        }

        $this->stdout("以下不受rbac权限控制的控制器(below controllers are not affected by rbac control)" . PHP_EOL);
        $this->stdout(implode(PHP_EOL, $backendAuth->getNoNeedRBACControllers()) . PHP_EOL . PHP_EOL, Console::FG_BLUE);
        $this->stdout("不受权限控制的路由(below routers are not affected by rbac control)" . PHP_EOL);
        $this->stdout(implode(PHP_EOL, $backendAuth->getNoNeedRBACRoutes()) . PHP_EOL, Console::FG_BLUE);

    }

    /**
     * Generate article different width thumbs(determined by Article::$thumbSizes)
     */
    public function actionGenArticleThumbnails()
    {
        $path = Yii::getAlias("@uploads/article/thumb/");
        $fp = opendir($path);
        while (($file = readdir($fp)) != false){
            if( $file == '.' || $file == '..' ) continue;
            $fullName = $path . $file;
            foreach (Article::$thumbSizes as $info){
                $thumbFullName = Util::getThumbName($fullName, $info['w'], $info['h']);
                $this->stdout(sprintf("generate %s width:%s height:%s\n", $fullName, $info['w'], $info['h']));
                Image::thumbnail($fullName, $info['w'], $info['h'])->save($thumbFullName);
            }
        }
    }

    /**
     * Delete empty directory or unused files.
     */
    public function actionDeleteUnusedFiles()
    {
        $rootPath = Yii::getAlias('@uploads');

        $articleThumbs = [];
        foreach (Article::find()->where(['<>', 'thumb', ''])->each(100) as $article) {
            $thumb = $rootPath . DIRECTORY_SEPARATOR . $article->thumb;
            $articleThumbs[] = $thumb;
            foreach (Article::$thumbSizes as $info){
                $articleThumbs[]  = Util::getThumbName($thumb, $info['w'], $info['h']);
            }
        }

        $articleContent = [];
        foreach (ArticleContent::find()->where(['<>', 'content', ''])->each(100) as $content) {
            $content->content = str_replace(Yii::$app->params['site']['url'], Yii::$app->params['site']['sign'], $content->content);
            preg_match_all('/<img.*src="(' . Yii::$app->params['site']['sign'] . '.*)"/isU', $content->content, $matches);
            if (! empty($matches[1])) {
                foreach ($matches[1] as $pic) {
                    $articleContent[] = str_replace(Yii::$app->params['site']['sign'], $rootPath, $pic);
                }
            }
        }

        $friendlyLinks = [];
        foreach (FriendlyLink::find()->where(['<>', 'image', ''])->each(100) as $link) {
            $friendlyLinks[] = $rootPath . $link->image;
        }

        $usingFiles = array_merge($articleThumbs, $articleContent, $friendlyLinks);

        $this->_deleteFileRecursive($rootPath, $usingFiles);

    }

    private function _deleteFileRecursive($directory, $usingFiles)
    {
        $it = new FileSystemIterator($directory);
        foreach ($it as $item) {
            if ($item->isDir()) {
                $nextLevelDir = $item->getPathName();
                if (count(scandir($nextLevelDir)) == 2) {
                    if (@rmdir($nextLevelDir)) {
                        $this->stdout("Delete directory " . $nextLevelDir . " success.\n", Console::FG_GREEN);
                    } else {
                        $this->stdout("Delete directory " . $nextLevelDir . " failed.\n", Console::FG_RED);
                    }
                } else {
                    $this->_deleteFileRecursive($nextLevelDir, $usingFiles);
                }
            } else {
                if (in_array($item->getFileName(), ['.gitignore', '.DS_Store'])) {
                    continue;
                }
                $fullFileName = $directory . DIRECTORY_SEPARATOR . $item->getFileName();
                if (! in_array($fullFileName, $usingFiles)) {
                    if (@unlink($fullFileName)) {
                        $this->stdout("Delete file " . $fullFileName . " success.\n", Console::FG_GREEN);
                    } else {
                        $this->stdout("Delete file " . $fullFileName . " failed.\n", Console::FG_RED);
                    }
                }
            }
        }
    }

    /**
     * Multi-language translate
     *
     * Use google translate auto generate multi-language
     * You should pass your own google API Key
     * Execute like `/path/to/yii feehi/translate apiKeyContent`
     *
     * @param string $apiKey
     * @throws \Exception
     */
    public function actionTranslate($apiKey="")
    {
        while( $apiKey == "" ){
            yii::$app->controller->stdout("Input your Google API Key :");
            $apiKey = trim(fgets(STDIN));
        }

        $url = "https://translation.googleapis.com/language/translate/v2?key=" . $apiKey;

        $dstLanguages = ["zh-TW", "fr", "ru", "es", "de", "it", "ja", "pt", "ko", "nl"];//繁体中文，法语，俄罗斯语，西班牙语，德语，意大利语，日语，葡萄牙，韩语，荷兰

        $translateItems = [
            [
                "messages" => array_flip(require Yii::getAlias("@yii/../../../backend/messages/en/menu.php")),
                "saveFile" =>  Yii::getAlias("@yii/../../../backend/messages/{language}/menu.php"),
            ],
            [
                "messages" => require Yii::getAlias("@yii/../../../backend/messages/zh/app.php"),
                "saveFile" => Yii::getAlias("@yii/../../../backend/messages/{language}/app.php"),
            ],
            [
                "messages" => require Yii::getAlias("@yii/../../../frontend/messages/zh/frontend.php"),
                "saveFile" => Yii::getAlias("@yii/../../../frontend/messages/{language}/frontend.php"),
            ],
            [
                "messages" => require Yii::getAlias("@yii/../../../install/messages/zh/install.php"),
                "saveFile" => Yii::getAlias("@yii/../../../install/messages/{language}/install.php"),
            ]
        ];

        foreach ($dstLanguages as $dstLanguage) {
            foreach ($translateItems as $item) {
                $fileName = str_replace("{language}", $dstLanguage, $item['saveFile']);
                $temp = pathinfo($fileName);
                if( !file_exists( $temp['dirname'] ) ){
                    mkdir($temp['dirname']);
                }

                $fileResult = [];
                foreach ($item['messages'] as $english => $chinese) {
                    $sourceLanguage = "en";
                    $message = $english;
                    if( $dstLanguage == "zh-TW" ){//to traditional chinese use simple chinese as source language
                        $sourceLanguage = "zh";
                        $message = $chinese;
                    }
                    $data = [
                        'q' => $message,
                        'source' => $sourceLanguage,
                        'target' => $dstLanguage,
                        'format' => 'text'
                    ];
                    $key = $english;
                    if( $temp['basename'] == "menu.php" ) {//menu translate source language is simple chinese
                        $key = $chinese;
                    }
                    $query = http_build_query($data);
                    $res = FileHelper::request($url . "&" . $query);
                    if (!isset($res[0])) {
                        $this->stderr(print_r($res, true) . print_r($data, true));
                        exit;
                    }
                    $response = json_decode($res[0], true);
                    if (!isset($response['data']['translations'][0]['translatedText'])) {
                        $this->stderr(print_r($response, true) . print_r($data, true));
                        exit;
                    }
                    $dstMessage = $response['data']['translations'][0]['translatedText'];
                    preg_match_all("/{.+}/isU", $key, $originMessageMatches);
                    preg_match_all("/{.+}/isU", $dstMessage, $dstMessageMatches);
                    if( count($originMessageMatches) != count($dstMessageMatches) ){
                        $this->stderr("match {attribute} failed" . print_r($originMessageMatches, true) . print_r($dstMessageMatches, true));
                        exit;
                    }
                    $dstMessage = str_replace($dstMessageMatches[0], $originMessageMatches[0], $dstMessage);
                    $fileResult[$key] = $dstMessage;
                    $this->stdout(sprintf("%s(%s) to %s(%s)\n", $message, $sourceLanguage, $dstMessage, $dstLanguage));
                }

                $this->stdout("write translate file " . $fileName . PHP_EOL, Console::FG_YELLOW);
                file_put_contents($fileName, "<?php \nreturn " . var_export($fileResult, true) . ";");
            }
        }
    }

    /**
     * Publish FeehiCMS
     */
    public function actionPublish()
    {
        $origin = Yii::getAlias('@console/../');
        $publishDir = $origin . '..' . DIRECTORY_SEPARATOR . 'publish';
        $temp = $publishDir . DIRECTORY_SEPARATOR;
        $needEmptyDirectories = [
            $temp . '.git',
            $temp . '.idea',
            $temp . 'tests',
            $temp . 'backend' . DIRECTORY_SEPARATOR . 'runtime',
            $temp . 'backend' . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'assets',
            $temp . 'frontend' . DIRECTORY_SEPARATOR . 'runtime',
            $temp . 'console' . DIRECTORY_SEPARATOR . 'runtime',
            $temp . 'install' . DIRECTORY_SEPARATOR . 'runtime',
            $temp . 'frontend' . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'assets',
            $temp . 'frontend' . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'uploads',
            $temp . 'frontend' . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'assets',
            $temp . 'frontend' . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'uploads',
            $temp . 'api' . DIRECTORY_SEPARATOR . 'runtime',
        ];
        FileHelper::copyDirectory($origin, $publishDir);
        foreach ($needEmptyDirectories as $v) {
            FileHelper::removeDirectory($v);
            if($v == $temp . 'tests') continue;
            FileHelper::createDirectory($v, 0777);
        }
        FileHelper::removeDirectory($publishDir . DIRECTORY_SEPARATOR . '.git');
        FileHelper::removeDirectory($publishDir . DIRECTORY_SEPARATOR . '.idea');
        if( file_exists($publishDir . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'install.lock') ) unlink($publishDir . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'install.lock');
        file_put_contents($temp . 'common' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'main-local.php', "<?php return [];?>" );
        //passthru("composer archive --dir=$publishDir --format=zip");
        $this->stdout('Publish Success, files in ' . $publishDir . "\n", Console::FG_GREEN);
    }
}