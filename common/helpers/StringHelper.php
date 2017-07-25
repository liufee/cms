<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-07-09 00:59
 */

namespace common\helpers;


class StringHelper extends \yii\helpers\StringHelper
{

    /**
     * 返回utf8编码的字符串
     *
     * @param $str
     * @return string
     */
    public static function encodingWithUtf8($str)
    {
        $cur_encoding = mb_detect_encoding($str);
        if ($cur_encoding == "UTF-8" && mb_check_encoding($str, "UTF-8")) {
            return $str;
        } else {
            return utf8_encode($str);
        }
    }
}