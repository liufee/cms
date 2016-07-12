<?php
/**
 * Ahthor: lf
 * Email: job@feehi.com
 * Blog: http://blog.feehi.com
 * Date: 2016/5/1811:13
 */
namespace console\controllers;


use yii;
use yii\console\Controller;
use Feehi\Http;
use console\controllers\scrawls\Jobbole;
use console\models\Article;
use feehi\libs\Help;


class ScrawlController extends Controller{

    private $fileName;

    public function actionBole()
    {
        $startTime = time();
        $articleNum = 0;
        $this->log("Started scrawl...", 'bole');
        $urls = [
            'http://web.jobbole.com/all-posts',
            'http://python.jobbole.com/all-posts',
            'http://www.importnew.com/all-posts',
        ];
        $obj = new Jobbole();
        foreach($urls as $url){
            if(strpos($url, 'all-posts')){
                $http = new Http();
                $tryCount = 0;
                do {
                    if($tryCount >= 10){
                        $this->log("Error at request $url for $tryCount times, exit");
                        exit(1);
                    }
                    if($tryCount > 0) sleep(1);
                    $html = $http->post($url);
                }while(strlen($html['body']) < 200);
                $totalPage = $obj->getTotalPage($html['body']);
            }else{

            }
            $this->log("Total of discovery $totalPage pages");
            for($i=$totalPage; $i>=1; $i--){
                if($i != 1){
                    $urlList = $url."/page/$i";
                }else{
                    $urlList = $url;
                }
                $tryCount = 0;
                do {
                    if($tryCount >= 3){
                        $this->log("Error at request $urlList for $tryCount times, so jumped");
                        continue;
                    }
                    if($tryCount > 0) sleep(1);
                    $html = $http->post($urlList);
                    $tryCount++;
                }while(strlen($html['body']) < 200);
                $articleUrls = $obj->getListUrl($html['body']);
                foreach($articleUrls as $articleUrl){
                    $tryCount = 0;
                    do {
                        if($tryCount >= 3){
                            $this->log("Error at request $articleUrl for $tryCount times, so jumped");
                            continue;
                        }
                        if($tryCount > 0) sleep(1);
                        $html = $http->post($articleUrl[0]);
                    }while(strlen($html['body']) < 200);
                    $data = $obj->getArticle($html['body']);
                    $article = new Article();
                    if( $temp = $article->findOne(['title'=>$data['title'], 'seo_description'=>$data['seo_description'], 'created_at'=>$articleUrl[2]]) ){
                        $this->log("$i -> {$articleUrl[0]} {$data['title']} has been fetched, so jumped");
                        continue;
                    }
                    foreach($data as $name => $value){
                        $article->$name = $value;
                    }
                    $article->articleOriginUrl = $articleUrl[0];
                    if( is_string($articleUrl[1]) ) $article->thumb = $articleUrl[1];
                    if( !is_integer($articleUrl[2]) ) $articleUrl[2] = 0;
                    $article->created_at = $articleUrl[2];
                    $article->seo_title = $article->title;
                    $article->seo_keywords = $article->tag;
                    $article->sumary = $article->seo_description;
                    if(strpos($url, 'http://web') === 0){
                        $article->cid = 7;//web
                    }else if(strpos($url, 'http://python') === 0){
                        $article->cid = 11;//python
                    }else if(strpos($url, 'http://www.importnew.com') === 0){
                        $article->cid = 12;//java
                    }
                    $article->type = Article::ARTICLE;
                    $article->status = Article::ARTICLE_PUBLISHED;
                    $article->content = Help::utf8Encoding($article->content);
                    if( !$article->save($data ) ){
                        $temp = $article->getErrors();
                        $errReason = '';
                        foreach ($temp as $rname => $rreson) {
                            $errReason .= $rname . '=>' . $rreson . ';';
                        }
                        $this->log("Saving page $i -> {$articleUrl[0]} error {$data['title']}. Reason $errReason");
                    }else{
                        $this->log("Saving page $i -> {$articleUrl[0]} success {$data['title']}");
                        $articleNum++;
                    }
                }
            }
        }
        $endTime = time();
        $this->log("Finished scrawl.Total use ".($endTime-$startTime)." seconds. Total scrawled ".$articleNum." articles");
    }

    private function log($str, $name=null)
    {
        if($name != null){
            Help::mk_dir(yii::getAlias("@runtime").'/logs/scrawl/'.$name);
            $this->fileName = yii::getAlias("@runtime").'/logs/scrawl/'.$name.'/'.date('Y-m-d-h-i-s').'.txt';
        }
        $log = "\r\n".date('Y-m-d H:i:s')."   ".$str."\r\n";
        $this->stdout($log);
        file_put_contents($this->fileName, $log, FILE_APPEND);
    }


}
