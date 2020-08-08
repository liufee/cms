<?php

use common\helpers\DbDriverHelper;

/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-08-04 17:27
 */

class SQLHelper
{
    public static function getRealSQL($sql, $options)
    {
        if( isset($options['table']) ) {
            $sql = str_replace(["###TABLE_NAME###"], [DbDriverHelper::getTableName("{{%user}}")], $sql);
        }
        return $sql;
    }
}