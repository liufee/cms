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

    public function actionA()
    {
        $tree = [
            ['id'=>1, 'name'=>'中国', 'parent_id'=>0],
            ['id'=>2, 'name'=>'美国', 'parent_id'=>0],
            ['id'=>3, 'name'=>'日本', 'parent_id'=>0],
            ['id'=>4, 'name'=>'湖南', 'parent_id'=>1],
            ['id'=>5, 'name'=>'华盛顿','parent_id'=>2],
            ['id'=>6, 'name'=>'广东', 'parent_id'=>1],
            ['id'=>7, 'name'=>'贵州', 'parent_id'=>1],
            ['id'=>8, 'name'=>'怀化', 'parent_id'=>4],
            ['id'=>9, 'name'=>'深圳', 'parent_id'=>6],
            ['id'=>10, 'name'=>'长沙', 'parent_id'=>4],
            ['id'=>11, 'name'=>'纽约', 'parent_id'=>2],
            ['id'=>12, 'name'=>'株洲', 'parent_id'=>4],
            ['id'=>13, 'name'=>'东莞', 'parent_id'=>6],
            ['id'=>14, 'name'=>'麻阳', 'parent_id'=>8],
        ];
        $obj = new \common\helpers\FamilyTree($tree);
        echo "中国的子节点\r\n";
        print_r($obj->getSons(1));
        echo "\r\n\r\n";
        echo "中国的所有子孙节点\r\n";
        print_r($obj->getDescendants(1));
        echo "\r\n\r\n";
        echo "麻阳的所有父节点\r\n";
        print_r($obj->getParents(14));
        echo "\r\n\r\n";
        echo "麻阳的所有祖先节点\r\n";
        print_r($obj->getAncectors(14));
    }

}