<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-05-18 15:35
 */

namespace console\models;

use Yii;
use backend\models\ArticleContent;
use yii\helpers\FileHelper;


class Article extends \backend\models\Article
{

    public $articleOriginUrl = '';//article origin url

    public function beforeSave($insert)
    {
        if ($this->flag_headline == null) {
            $this->flag_headline = 0;
        }
        if ($this->flag_recommend == null) {
            $this->flag_recommend = 0;
        }
        if ($this->flag_slide_show == null) {
            $this->flag_slide_show = 0;
        }
        if ($this->flag_special_recommend == null) {
            $this->flag_special_recommend = 0;
        }
        if ($this->flag_roll == null) {
            $this->flag_roll = 0;
        }
        if ($this->flag_bold == null) {
            $this->flag_bold = 0;
        }
        if ($this->flag_picture == null) {
            $this->flag_picture = 0;
        }
        $this->tag = str_replace('，', ',', $this->tag);
        $this->seo_keywords = str_replace('，', ',', $this->seo_keywords);
        if ($insert) {
            $this->author_id = 0;
            $this->author_name = 'robot';

        } else {
            $this->updated_at = time();
        }
        $this->scrawThumb();
        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $contentModel = new ArticleContent();
            $contentModel->aid = $this->id;
        } else {
            if ($this->content === null) {
                return;
            }
            $contentModel = ArticleContent::findOne(['aid' => $this->id]);
            if ($contentModel == null) {
                $contentModel = new ArticleContent();
                $contentModel->aid = $this->id;
            }
        }
        $this->scrawlPic();
        $contentModel->content = $this->content;
        $contentModel->save();
    }

    public function needScrawlPic($url)
    {
        if (strpos($url, "upaiyun.com")) {
            return true;
        }
        if (strpos($url, '/') === 0) {
            return true;
        }
        return false;
    }

    public function scrawThumb()
    {
        if ($this->needScrawlPic($this->thumb)) {
            $path = Yii::getAlias("@thumb/robot/");
            if (! file_exists($path)) {
                FileHelper::createDirectory($path);
            }
            $ext = pathinfo($this->thumb)['extension'];
            $fileName = uniqid() . '.' . $ext;
            if (strpos($this->thumb, '/') === 0) {
                $temp = parse_url($this->articleOriginUrl);
                $this->thumb = $temp['scheme'] . '://' . $temp['host'] . $this->thumb;
            }
            $imgBin = file_get_contents($this->thumb);
            if (file_put_contents($path . $fileName, $imgBin)) {
                $temp = explode("uploads/", $path . $fileName);
                $this->thumb = Yii::$app->params['site']['sign'] . "/uploads/" . $temp[1];
            }
        }
    }

    public function scrawlPic()
    {
        preg_match_all("/<img.*?src=[\'\"](.*?)[\'\"]/", $this->content, $matches);
        if (count($matches[1]) <= 0) {
            return;
        }
        $replace = [];
        foreach ($matches[1] as $key => $val) {
            if (! $this->needScrawlPic($val)) {
                unset($matches[1][$key]);
                continue;
            }
            $path = Yii::getAlias("@article/robot/") . date('Y/m/');
            if (! file_exists($path)) {
                FileHelper::createDirectory($path);
            }
            $ext = pathinfo($val)['extension'];
            $fileName = uniqid() . '.' . $ext;
            if (strpos($val, '/') === 0) {
                $temp = parse_url($this->articleOriginUrl);
                $val = $temp['scheme'] . '://' . $temp['host'] . $val;
            }
            $imgBin = file_get_contents($val);
            if (! file_put_contents($path . $fileName, $imgBin)) {
                unset($matches[1][$key]);
            } else {
                $temp = explode("uploads/", $path . $fileName);
                array_push($replace, Yii::$app->params['site']['sign'] . "/uploads/" . $temp[1]);
            }
        }
        $this->content = str_replace($matches[1], $replace, $this->content);
    }
}