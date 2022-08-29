<?php

namespace common\components;

use Yii;
use yii\helpers\Url;

class Response extends \yii\web\Response
{
    public function redirect($url, $statusCode = 302, $checkAjax = true)
    {
        if (is_array($url) && isset($url[0])) {
            // ensure the route is absolute
            $url[0] = '/' . ltrim($url[0], '/');
        }
        $request = Yii::$app->getRequest();
        $url = Url::to($url);
        if(  strpos($url, "://") !== false ){
            $newURL = "";
            $array = parse_url(Yii::$app->getUser()->getReturnUrl());
            isset($array['path']) && $newURL .= $array['path'];
            isset($array['query']) && $newURL .= "?" . $array['query'];
            if ($newURL == ""){
                $url = "/";
            }else{
                $url = $newURL;
            }
        }
        if ($checkAjax) {
            if ($request->getIsAjax()) {
                if (in_array($statusCode, [301, 302]) && preg_match('/Trident\/|MSIE[ ]/', (string)$request->userAgent)) {
                    $statusCode = 200;
                }
                if ($request->getIsPjax()) {
                    $this->getHeaders()->set('X-Pjax-Url', $url);
                } else {
                    $this->getHeaders()->set('X-Redirect', $url);
                }
            } else {
                $this->getHeaders()->set('Location', $url);
            }
        } else {
            $this->getHeaders()->set('Location', $url);
        }

        $this->setStatusCode($statusCode);

        return $this;
    }
}