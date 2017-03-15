<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-14 15:42
 */

namespace backend\controllers;

use yii;

class ErrorController extends BaseController
{

    public function actionIndex()
    {
        if (($exception = Yii::$app->getErrorHandler()->exception) === null) {
            // action has been invoked not from error handler, but by direct route, so we display '404 Not Found'
            $exception = new HttpException(404, Yii::t('yii', 'Page not found.'));
        }

        if ($exception instanceof HttpException) {
            $code = $exception->statusCode;
        } else {
            $code = $exception->getCode();
        }
        //if ($exception instanceof Exception) {
        $name = $exception->getName();
        //} else {
        //$name = $this->defaultName ?: Yii::t('yii', 'Error');
        //}
        if ($code) {
            $name .= " (#$code)";
        }

        //if ($exception instanceof UserException) {
        $message = $exception->getMessage();
        //} else {
        //$message = $this->defaultMessage ?: Yii::t('yii', 'An internal server error occurred.');
        //}
        $statusCode = $exception->statusCode ? $exception->statusCode : 500;
        if (Yii::$app->getRequest()->getIsAjax()) {
            return "$name: $message";
        } else {
            return $this->render('index', [
                'code' => $statusCode,
                'name' => $name,
                'message' => $message,
                'exception' => $exception,
            ]);
        }
    }

    public function actionForbidden()
    {
        Yii::$app->getResponse()->statusCode = 403;
        return $this->render('error', [
            'code' => '403',
            'name' => yii::t('app', 'Lack of authority'),
            'message' => yii::t('app', 'You are not allowed to perform this action'),
        ]);
    }

    public function actionNotFound()
    {
        Yii::$app->getResponse()->statusCode = 404;
        return $this->render('error', [
            'code' => '404',
            'name' => "Not Found",
            'message' => yii::t('app', 'Page does not exist'),
        ]);
    }

}