<?php
namespace backend\actions\helpers;

use Yii;
use yii\base\Exception;
use yii\base\Model;

/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-01-31 21:19
 */

class Helper
{
    public static function getPrimaryKeys($primaryKeyIdentity, $primaryKeyFromMethod)
    {
        $primaryKeys = [];

        if( !empty( $primaryKeyIdentity ) ){

            if( is_string($primaryKeyIdentity) ){
                $primaryKeyIdentity = [$primaryKeyIdentity];
            }else if( !is_array($primaryKeyIdentity) ){
                throw new Exception("primaryKeyIdentity must be string or array");
            }

            foreach ($primaryKeyIdentity as $identity){
                if( $primaryKeyFromMethod == "GET" ){
                    $primaryKeys[] =Yii::$app->getRequest()->get($identity, null);
                }else if( $primaryKeyFromMethod == "POST" ){
                    $primaryKeys[] = Yii::$app->getRequest()->post($identity, null);
                }else{
                    throw new Exception('primaryKeyFromMethod must be GET or POST');
                }
            }
        }

        return $primaryKeys;

    }

    public static function getErrorString($result)
    {
        if( !is_array($result) ){
            $results = [$result];
        }else{
            $results = $result;
        }
        $error = "";
        foreach ($results as $result) {

            if ($result instanceof Model) {//if returns a model, will call getErrors() get the error description string
                $items = $result->getErrors();
                foreach ($items as $item) {
                    foreach ($item as $e) {
                        $error .= $e . "<br>";
                    }
                }
                $error = rtrim($error, "<br>");
            } else if (is_string($result)) {//if returns a string, they will be the error description
                $error = $result;
            } else {
                throw new Exception("doCreate/doUpdate/doDelete/doSort closure must return boolean, yii\base\Model or string");
            }

        }

        return $error;
    }
}