<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2018-08-15 23:30
 */
namespace common\behaviors;

use Yii;
use yii\gii\Module;
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

        if (Yii::$app->getRequest()->getIsAjax()) {
            $response->format = $this->defaultAjaxResponseFormat;
        }else{
            $response->format = $this->defaultResponseFormat;
        }

        $acceptTypes = Yii::$app->getRequest()->getAcceptableContentTypes();
        $types = array_keys($acceptTypes);
        if (isset($types[0])) {
            foreach ($types as $type) {
                $found = false;
                switch ($type) {
                    case "text/plain":
                        $response->format = Response::FORMAT_RAW;
                        $found = true;
                        break;
                    case "application/html":
                    case "text/html":
                        if( $this->isApi ){
                            $response->format = $this->defaultResponseFormat;
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
        }

        $exception = Yii::$app->getErrorHandler()->exception;
        if ( YII_DEBUG && $exception !== null && !is_array($response->data)) {//debug env and shows error page
            $response->format = Response::FORMAT_HTML;
        }
    }
}