<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-05-18 11:13
 */
namespace console\controllers;


use yii;
use yii\console\Controller;
use Feehi\Http;
use console\controllers\scrawls\Jobbole;
use console\models\Article;
use yii\helpers\FileHelper;
use common\models\Category;
use common\helpers\StringHelper;


class ScrawlController extends Controller
{

    private $fileName;

    public function actionBole($type = 'all')
    {
        $startTime = time();
        $articleNum = 0;
        $this->log("Started scrawl...", 'bole');
        $urls = [
            'http://web.jobbole.com/all-posts',
            'http://python.jobbole.com/all-posts',
            'http://www.importnew.com/all-posts',
            'http://blog.jobbole.com/category/php-programmer',
        ];
        $obj = new Jobbole();
        foreach ($urls as $url) {
            if (strpos($url, 'all-posts') || strpos($url, 'php-programmer')) {
                $http = new Http();
                $tryCount = 0;
                do {
                    if ($tryCount >= 10) {
                        $this->log("Error at request $url for $tryCount times, exit");
                        exit(1);
                    }
                    if ($tryCount > 0) {
                        sleep(1);
                    }
                    $html = $http->post($url);
                } while (strlen($html['body']) < 200);
                $totalPage = $obj->getTotalPage($html['body']);
            } else {

            }
            if ($type == 'new') {
                $totalPage = 1;
            }
            $this->log("Total of discovery $totalPage pages");
            for ($i = $totalPage; $i >= 1; $i--) {
                if ($i != 1) {
                    $urlList = $url . "/page/$i";
                } else {
                    $urlList = $url;
                }
                $tryCount = 0;
                do {
                    if ($tryCount >= 3) {
                        $this->log("Error at request $urlList for $tryCount times, so jumped");
                        continue;
                    }
                    if ($tryCount > 0) {
                        sleep(1);
                    }
                    $html = $http->post($urlList);
                    $tryCount++;
                } while (strlen($html['body']) < 200);
                $articleUrls = $obj->getListUrl($html['body']);
                foreach ($articleUrls as $articleUrl) {
                    $tryCount = 0;
                    do {
                        if ($tryCount >= 3) {
                            $this->log("Error at request $articleUrl for $tryCount times, so jumped");
                            continue;
                        }
                        if ($tryCount > 0) {
                            sleep(1);
                        }
                        $html = $http->post($articleUrl[0]);
                    } while (strlen($html['body']) < 200);
                    $data = $obj->getArticle($html['body']);
                    $article = new Article(['scenario' => 'article']);
                    if ($temp = $article->findOne([
                        'title' => $data['title'],
                        'seo_description' => $data['seo_description'],
                        'created_at' => $articleUrl[2]
                    ])
                    ) {
                        $this->log("$i -> {$articleUrl[0]} {$data['title']} has been fetched, so jumped");
                        continue;
                    }
                    foreach ($data as $name => $value) {
                        $article->$name = $value;
                    }
                    $article->articleOriginUrl = $articleUrl[0];
                    if (is_string($articleUrl[1])) {
                        $article->thumb = $articleUrl[1];
                    }
                    if (! is_integer($articleUrl[2])) {
                        $articleUrl[2] = 0;
                    }
                    $article->created_at = $articleUrl[2];
                    $article->seo_title = $article->title;
                    $article->seo_keywords = $article->tag;
                    $article->summary = $article->seo_description;
                    $categories = Category::find()->asArray()->all();
                    $temp = [];
                    foreach ($categories as $c) {
                        if (in_array($c['name'], ['javascript', 'python', 'java', 'php'])) {
                            $temp[$c['name']] = $c['id'];
                        }
                    }
                    if (strpos($url, 'http://web') === 0) {
                        $article->cid = $temp['javascript'];//web
                        $language = 'javascript';
                    } else {
                        if (strpos($url, 'http://python') === 0) {
                            $article->cid = $temp['python'];//python
                            $language = 'python';
                        } else {
                            if (strpos($url, 'http://www.importnew.com') === 0) {
                                $article->cid = $temp['java'];//java
                                $language = 'java';
                            } else {
                                if (strpos($url, 'php-programmer')) {
                                    $article->cid = $temp['php'];
                                    $language = 'php';
                                }
                            }
                        }
                    }
                    $article->type = Article::ARTICLE;
                    $article->status = Article::ARTICLE_PUBLISHED;
                    $article->content = StringHelper::encodingWithUtf8($article->content);
                    $article->content = preg_replace("/<table.*?class=\"crayon-table\">.*?<\/table>/", '', $article->content);
                    if ($article->cid == $temp['java']) {
                        $article->content = str_replace([
                            "          ",
                            "         ",
                            "      ",
                            "  "
                        ], "\r\n", $article->content);
                    } else {
                        $function = function ($matches) use ($language) {
                            $str = str_replace($matches[0], "<pre class=\"brush:{$language};toolbar:false\">", $matches[0]);
                            if (isset($matches[1])) {
                                $str .= str_replace([
                                    "&nbsp; &nbsp; &nbsp;",
                                    "&nbsp; &nbsp;",
                                    "&nbsp;"
                                ], "\r\n", $matches[1]);
                                $str = str_replace("    ", "\r\n", $str);
                                $str = str_replace("/textarea&gt", '', $str);
                            }
                            return $str;
                        };
                        $article->content = preg_replace_callback("/<textarea wrap=\"soft\" class=\"crayon-plain print-no\".*?>(.*?)</", $function, $article->content);
                        $article->content = preg_replace("/\/textarea>/", "</pre>", $article->content);
                    }
                    if (! $article->save($data)) {
                        $temp = $article->getErrors();
                        $errReason = '';
                        foreach ($temp as $rname => $rreson) {
                            $errReason .= $rname . '=>' . $rreson[0] . ';';
                        }
                        $this->log("Saving page $i -> {$articleUrl[0]} error {$data['title']}. Reason $errReason");
                    } else {
                        $this->log("Saving page $i -> {$articleUrl[0]} success {$data['title']}");
                        $articleNum++;
                    }
                }
            }
        }
        $endTime = time();
        $this->log("Finished scrawl.Total use " . ($endTime - $startTime) . " seconds. Total scrawled " . $articleNum . " articles");
    }

    private function log($str, $name = null)
    {
        if ($name != null) {
            FileHelper::createDirectory(yii::getAlias("@runtime") . '/logs/scrawl/' . $name);
            $this->fileName = yii::getAlias("@runtime") . '/logs/scrawl/' . $name . '/' . date('Y-m-d-h-i-s') . '.txt';
        }
        $log = "\r\n" . date('Y-m-d H:i:s') . "   " . $str . "\r\n";
        $this->stdout($log);
        file_put_contents($this->fileName, $log, FILE_APPEND);
    }


}
