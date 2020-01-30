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

    public $defaultResponseFormat = Response::FORMAT_HTML;

    public function events()
    {
        return [
            Response::EVENT_BEFORE_SEND => [$this, 'beforeSend'],
        ];
    }

    public function beforeSend()
    {
        /** @var Response $response */
        $response = $this->owner;
        if( Yii::$app->getRequest()->getIsAjax() ){
            $response->format = Response::FORMAT_JSON;
            return;
        }
        $acceptTypes = Yii::$app->getRequest()->getAcceptableContentTypes();
        $types = array_keys( $acceptTypes );
        isset($types[0]) && $types[0] == 'text/html' && $response->format = $this->defaultResponseFormat;
        if( Yii::$app->controller && Yii::$app->controller->module instanceof Module ){
            $response->format = Response::FORMAT_HTML;
        }
        $exception = Yii::$app->getErrorHandler()->exception;
        if( $exception !== null && !is_array($response->data ) ){
            $response->format = Response::FORMAT_HTML;
        }

    }
}