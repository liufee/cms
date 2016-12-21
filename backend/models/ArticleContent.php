<?php
/**
 * Ahthor: lf
 * Email: job@feehi.com
 * Blog: http://blog.feehi.com
 * Date: 2016/4/1111:30
 */
namespace backend\models;

use yii;

class ArticleContent extends \common\models\ArticleContent
{
    public function beforeSave($insert)
    {
        parent::beforeSave($insert);
        //$this->downloadImageToLocal();
        return true;
    }

    protected function downloadImageToLocal()
    {
        $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
        preg_match_all($pattern, $this->content, $matches);//var_dump($matches[1]);die;
        foreach($matches[1] as $val){
            if(strpos($val, 'http') !== 0) continue;
            $pic = file_get_contents($val);
            $base = Yii::getAlias('@webroot');
            $path = yii::$app->params['uploadPath']['article']['figure'].'/'.date('Y').'/'.date('m').'/'.date('d').'/'.$this->aid.'/';
            $extension = substr($val, strrpos($val, '.')+1);
            $fileName = date('YmdHis').rand(0,99999999).'.'.$extension;
            Help::mk_dir($base.$path);
            file_put_contents($base.$path.$fileName, $pic);

            $key = $path.$fileName;//echo $key;die;
            $ret = yii::$app->alioss->uploadFile($key, $base.$path.$fileName);
            if( $ret !== null ) yii::getLogger()->log("Article figure upload to ali oss faield,article aid=>{$this->aid} title=>{$this->title},error_info=>{$ret}", Logger::LEVEL_ERROR);
            //$url = "http://img.feehi.com/".$key;
            $this->content = str_replace($val, $path.$fileName, $this->content);
        }
    }

    public function replaceToCdnUrl()
    {
        if( !isset(yii::$app->params['cdnUrl']) || yii::$app->params['cdnUrl'] == '' ) return true;
        if(strpos($this->content, 'src="/uploads"')){
            $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
            preg_match_all($pattern, $this->content, $matches);
            $matches[1] = array_unique($matches[1]);
            foreach($matches[1] as $v){
                $this->content = str_replace($v, yii::$app->params['cdnUrl'].$v, $this->content);
            }
        }else{
            $this->content = str_replace(yii::$app->params['site']['url'], yii::$app->params['cdnUrl'], $this->content);
        }
        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if($insert) {
            preg_match_all('/<img.*src="('.yii::$app->params['site']['sign'].'.*)"/isU', $this->content, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $v) {
                    $fileUsageModel = new FileUsage();
                    $fileUsageModel->useFile($v, $this->aid, FileUsage::TYPE_ARTICLE_BODY, 1);
                }
            }
        }else{
            if(isset($changedAttributes['content'])) preg_match_all('/<img.*src="('.yii::$app->params['site']['sign'].'.*)"/isU', $changedAttributes['content'], $matchesOld);
            preg_match_all('/<img.*src="('.yii::$app->params['site']['sign'].'.*)"/isU', $this->content, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $v) {
                    if (!empty($matchesOld) && !in_array($v, $matchesOld[1]) ) {
                        $fileUsageModel = new FileUsage();
                        $fileUsageModel->useFile($v, $this->aid, FileUsage::TYPE_ARTICLE_BODY, 1);
                    }
                }
            }
            if(!empty($matchesOld[1])){
                $noneUsePics = array_diff($matchesOld[1], $matches[1]);
                foreach ($noneUsePics as $v) {
                    $fileUsageModel = new FileUsage();
                    $uri = str_replace(yii::$app->params['site']['url'], yii::$app->params['site']['sign'], $v);
                    $fileUsageModel->cancelUseFile($uri, $this->aid, FileUsage::TYPE_ARTICLE_BODY);
                }
            }
        }
        return true;
    }

    public function afterDelete()
    {
        $this->content = str_replace(yii::$app->params['site']['url'], yii::$app->params['site']['sign'], $this->content);
        preg_match_all('/<img.*src="('.yii::$app->params['site']['sign'].'.*)"/isU', $this->content, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $v) {
                $fileUsageModel = new FileUsage();
                $uri = str_replace(yii::$app->params['site']['url'], yii::$app->params['site']['sign'], $v);
                $fileUsageModel->cancelUseFile($uri, $this->aid, FileUsage::TYPE_ARTICLE_BODY);
            }
        }
        return true;
    }
}