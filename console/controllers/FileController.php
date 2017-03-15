<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-14 21:07
 */

namespace console\controllers;

use yii;
use backend\models\ArticleContent;
use common\helpers\FileHelper;
use common\models\FriendLink;
use common\models\Article;
use yii\helpers\Console;

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
        foreach (FriendLink::find()->where(['<>', 'image', ''])->each(100) as $link) {
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

}