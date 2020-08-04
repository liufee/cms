<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-08-04 15:40
 */

namespace common\helpers;


use Yii;

class DbDriverHelper
{
    public static function isMSSQL()
    {
        return Yii::$app->getDb()->getDriverName() === 'mssql' || Yii::$app->getDb()->getDriverName() === 'sqlsrv' || Yii::$app->getDb()->getDriverName() === 'dblib';
    }

    public static function isMySQL()
    {
        return Yii::$app->getDb()->getDriverName() === 'mysql';
    }

    public static function isOracle()
    {
        return Yii::$app->getDb()->getDriverName() === 'oci';
    }

    public static function isPgSQL()
    {
        return Yii::$app->getDb()->getDriverName() === 'pgsql';
    }

    public static function isSqlite()
    {
        return Yii::$app->getDb()->getDriverName() === 'sqlite';
    }

    public static function getTableName($tableName, $tablePrefix=null){
        if( $tablePrefix === null ){
            $tablePrefix = Yii::$app->getDb()->tablePrefix;
        }
        return preg_replace("/{{%(\w+)}}/isu", $tablePrefix . "$1", $tableName);
    }
}