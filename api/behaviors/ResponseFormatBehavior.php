<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2018-08-15 23:30
 */
namespace api\behaviors;

use Yii;
use yii\web\Response;

class ResponseFormatBehavior extends \yii\base\Behavior
{

    public $negotiate = true;

    public $format = Response::FORMAT_JSON;

    /** @var Response $response */
    private $_response;

    public function events()
    {
        return [
            Response::EVENT_BEFORE_SEND => [$this, 'beforeSend'],
        ];
    }

    public function beforeSend()
    {
        $this->_response = Yii::$app->getResponse();

        if( !$this->negotiate ){
            $this->_response->format = $this->format;
        }else {
            $this->negotiate();
        }

    }

    private function negotiate()
    {
        $acceptTypes = Yii::$app->getRequest()->getAcceptableContentTypes();
        $acceptTypes = array_keys($acceptTypes);
        foreach ($acceptTypes as $acceptType){
            switch ($acceptType) {
                case "text/plain":
                    $this->_response->format = Response::FORMAT_RAW;
                    break;
                case "application/html":
                case "text/html":
                case "*/*":
                    $this->_response->format = $this->format;
                    break;
                case "application/json":
                case "text/json":
                    $this->_response->format = Response::FORMAT_JSON;
                    break;
                case "application/xml":
                case "text/xml":
                    $this->_response->format = Response::FORMAT_XML;
                    break;
            }
        }
    }
}