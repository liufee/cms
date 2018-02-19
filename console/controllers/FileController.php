<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-14 21:07
 */

namespace console\controllers;

use common\helpers\Util;
use yii;
use backend\models\ArticleContent;
use common\models\FriendlyLink;
use common\models\Article;
use yii\helpers\Console;
use yii\helpers\FileHelper;
use yii\imagine\Image;

set_time_limit(0);

/**
 * File attach management
 */
class FileController extends \yii\console\Controller
{
    private $unusedFiles = [];

    /**
     * Delete unused files and empty directory.
     */
    public function actionDeleteUnused()
    {
        $articeThumb = [];
        $rootPath = yii::getAlias('@frontend/web');
        foreach (Article::find()->where(['<>', 'thumb', ''])->each(100) as $artice) {
            $articeThumb[] = str_replace(yii::$app->params['site']['url'], $rootPath, $artice->thumb);
        }

        $articleContent = [];
        foreach (ArticleContent::find()->where(['<>', 'content', ''])->each(100) as $content) {
            $content->content = str_replace(yii::$app->params['site']['url'], yii::$app->params['site']['sign'], $content->content);
            preg_match_all('/<img.*src="(' . yii::$app->params['site']['sign'] . '.*)"/isU', $content->content, $matches);
            if (! empty($matches[1])) {
                foreach ($matches[1] as $pic) {
                    $articleContent[] = str_replace(yii::$app->params['site']['sign'], $rootPath, $pic);
                }
            }
        }

        $friendlink = [];
        foreach (FriendlyLink::find()->where(['<>', 'image', ''])->each(100) as $link) {
            $friendlink[] = $rootPath . $link->image;
        }

        $this->unusedFiles = array_merge($articeThumb, $articleContent, $friendlink);

        $this->_deleteFileRecursive(yii::getAlias('@uploads'));

    }

    private function _deleteFileRecursive($path)
    {
        $it = new \FileSystemIterator($path);
        foreach ($it as $value) {
            if ($value->isDir()) {
                $nextLevelDir = $path . DIRECTORY_SEPARATOR . $value->getFilename();
                if (count(scandir($nextLevelDir)) == 2) {
                    if (@rmdir($nextLevelDir)) {
                        $this->stdout("Delete directory " . $nextLevelDir . " success.\n", Console::FG_GREEN);
                    } else {
                        $this->stdout("Delete directory " . $nextLevelDir . " failed.\n", Console::FG_RED);
                    }
                } else {
                    $this->_deleteFileRecursive($nextLevelDir);
                }
            } else {
                if (in_array($value->getFileName(), ['.gitignore', '.DS_Store'])) {
                    continue;
                }
                $fullFileName = $path . DIRECTORY_SEPARATOR . $value->getFileName();
                if (! in_array($fullFileName, $this->unusedFiles)) {
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
     * 打包发布cms
     *
     */
    public function actionPublish()
    {
        $origin = yii::getAlias('@console/../');
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
            $temp . 'common' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR .'conf',
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
        FileHelper::createDirectory($publishDir . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR .'conf', 0777);
        if( file_exists($publishDir . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR .'conf' . DIRECTORY_SEPARATOR . 'install.lock') ) unlink($publishDir . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR .'conf' . DIRECTORY_SEPARATOR . 'install.lock');
        file_put_contents($temp . 'common' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'main-local.php', "<?php return [];?>" );
        //passthru("composer archive --dir=$publishDir --format=zip");
        $this->stdout('Copy Success' . "\n", Console::FG_GREEN);
    }

    public function actionTest()
    {
        $result = \common\models\Menu::find()->asArray()->all();
        $columns = array_keys($result[0]);
        $str = "[";
        foreach ($columns as $column){
            $str .= "'{$column}',";
        }
        $str = rtrim($str, ',');
        $str .= "]";
        file_put_contents('db.txt', $str);

        $str = "\n\n[\n";
        foreach ($result as $val){
            $str .= "  [";
            foreach ($val as $v){
                    $str .= "'{$v}',";
            }
            $str = rtrim($str, ',');
            $str .= "],\n";
        }
        $str .= "]\n";
        //var_dump($str);exit;
        file_put_contents('db.txt', $str, FILE_APPEND);exit;
        $str = '';
        $authManager = yii::$app->getAuthManager();
        $permissions = $authManager->getPermissions();
        $columns = array_keys((array)$permissions['/setting/website:GET']);
        $str = "[";
        foreach ($columns as $column){
            $str .= "'{$column}',";
        }
        $str .= "]\n";
        foreach ($permissions as $permission){
            $str .= "  [";
            $i = 1;
            foreach ($permission as $v){
                if( $i == 5 ){
                    $str .= "{$v},";
                }else {
                    $str .= "'{$v}',";
                }
                $i++;
            }
            $str = rtrim($str, ',');
            $str .= "],\n";
        }
        file_put_contents('db.txt', $str);
    }

    public function actionTag()
    {
        $articles = \common\models\Article::find()->asArray()->all();
        $columns = ['aid', 'key', 'value', 'created_at'];
        $str = "[";
        foreach ($columns as $column){
            $str .= "'{$column}',";
        }
        $str = rtrim($str, ',');
        $str .= "]";
        file_put_contents('db.txt', $str);

        $str = "\n\n[\n";
        $aaa = [];
        foreach ($articles as $article){
            $tags = explode(',', $article['tag']);
            foreach ($tags as $tag){
                if( $tag == '' ) continue;
                $aaa[] = [
                    "aid" => $article['id'],
                    "key" => "tag",
                    "value" => $tag,
                    "created_at" => time(),
                ];
            }
        }
        foreach ($aaa as $val){
            $str .= "  [";
            foreach ($val as $v){
                $str .= "'{$v}',";
            }
            $str = rtrim($str, ',');
            $str .= "],\n";
        }
        $str .= "]\n";
        //var_dump($str);exit;
        file_put_contents('db.txt', $str, FILE_APPEND);exit;
        $str = '';
        $authManager = yii::$app->getAuthManager();
        $permissions = $authManager->getPermissions();
        $columns = array_keys((array)$permissions['/setting/website:GET']);
        $str = "[";
        foreach ($columns as $column){
            $str .= "'{$column}',";
        }
        $str .= "]\n";
        foreach ($permissions as $permission){
            $str .= "  [";
            $i = 1;
            foreach ($permission as $v){
                if( $i == 5 ){
                    $str .= "{$v},";
                }else {
                    $str .= "'{$v}',";
                }
                $i++;
            }
            $str = rtrim($str, ',');
            $str .= "],\n";
        }
        file_put_contents('db.txt', $str);
    }

    public function actionGenArticleThumbnails()
    {
        $path = yii::getAlias("@uploads/article/thumb/");
        $fp = opendir($path);
        while (($file = readdir($fp)) != false){
            if( $file == '.' || $file == '..' ) continue;
            $fullName = $path . $file;
            foreach (Article::$thumbSizes as $info){
                $thumbFullName = Util::getThumbName($fullName, $info['w'], $info['h']);
                Image::thumbnail($fullName, $info['w'], $info['h'])->save($thumbFullName);
            }
        }
    }

}