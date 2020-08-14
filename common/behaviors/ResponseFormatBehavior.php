<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2018-08-15 23:30
 */
namespace common\behaviors;

use Yii;
use yii\web\Response;

class ResponseFormatBehavior extends \yii\base\Behavior
{

    public $isApi = false;

    public $autoNegotiate = true;
    public $responseFormat = Response::FORMAT_HTML;
    public $ajaxResponseFormat = Response::FORMAT_JSON;


    public $defaultResponseFormat = Response::FORMAT_HTML;
    public $defaultAjaxResponseFormat = Response::FORMAT_JSON;

    public function events()
    {
        return [
            Response::EVENT_BEFORE_SEND => [$this, 'beforeSend'],
        ];
    }

    public function beforeSend()
    {
        if( !$this->autoNegotiate ){
            $this->force();
        }else {
            $this->negotiate();
        }

    }

    private function force()
    {
        /** @var Response $response */
        $response = $this->owner;
        if( Yii::$app->getRequest()->getIsAjax() ){
            $response->format = $this->ajaxResponseFormat;
        }else{
            $response->format = $this->responseFormat;
        }
    }

    private function negotiate()
    {
        /** @var Response $response */
        $response = $this->owner;

        $acceptTypes = Yii::$app->getRequest()->getAcceptableContentTypes();
        $acceptTypes = array_keys($acceptTypes);
        $found = false;
        foreach ($acceptTypes as $acceptType){
            switch ($acceptType) {
                case "text/plain":
                    $response->format = Response::FORMAT_RAW;
                    $found = true;
                    break;
                case "application/html":
                case "text/html":
                    if( $this->isApi ){//api default response format(makes web browsers request api get a correct response format)
                        $response->format = $this->defaultResponseFormat;
                    }else if( Yii::$app->getRequest()->getIsAjax() && is_array(Yii::$app->getResponse()->content) && Yii::$app->getResponse()->format === $this->defaultResponseFormat ){
                        //(backend,frontend) ajax if returns an array and not set response format, will use the defaultAjaxResponseFormat
                        $response->format = $this->defaultAjaxResponseFormat;
                    }else{
                        $response->format = Response::FORMAT_HTML;
                    }
                    $found = true;
                    break;
                case "application/json":
                case "text/json":
                    $response->format = Response::FORMAT_JSON;
                    $found = true;
                    break;
                case "application/xml":
                case "text/xml":
                    $response->format = Response::FORMAT_XML;
                    $found = true;
                    break;
            }
            if( $found ){
                break;
            }
        }

        if( $found === false ){
            if (Yii::$app->getRequest()->getIsAjax()) {
                $response->format = $this->defaultAjaxResponseFormat;
            }else{
                $response->format = $this->defaultResponseFormat;
            }
        }

        $exception = Yii::$app->getErrorHandler()->exception;
        if ( YII_DEBUG && $exception !== null && !is_array($response->data)) {//debug env and shows error page
            $response->format = Response::FORMAT_HTML;
        }
    }
}