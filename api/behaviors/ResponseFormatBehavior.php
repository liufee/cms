<?php
/**
 * Created by PhpStorm.
 * User: lf
 * Date: 2018/8/15
 * Time: 下午11:30
 */
namespace api\behaviors;

use Yii;
use yii\web\Response;

class ResponseFormatBehavior extends \yii\base\Behavior
{

    public function events()
    {
        return [
            Response::EVENT_BEFORE_SEND => [$this, 'beforeSend'],
        ];
    }

    public function beforeSend()
    {
        $acceptTypes = Yii::$app->getRequest()->getAcceptableContentTypes();
        $types = array_keys( $acceptTypes );
        /** @var Response $response */
        $response = $this->owner;
        isset($types[0]) && $types[0] == 'text/html' && $response->format = Response::FORMAT_JSON;
    }
}