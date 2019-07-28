<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2018-12-05 12:23
 */

namespace console\helpers;

class FileHelper extends \yii\helpers\FileHelper
{
    public static function unzip($zipFile, $path)
    {
        if (!file_exists($zipFile)) {
            throw new Exception("File not exists " . $zipFile);
        }
        $resource = zip_open($zipFile);

        while ($dirResource = zip_read($resource)) {
            if (zip_entry_open($resource, $dirResource)) {
                $file_name = $path . zip_entry_name($dirResource);
                $file_path = substr($file_name, 0, strrpos($file_name, "/"));
                if (!is_dir($file_path)) {
                    mkdir($file_path, 0777, true);
                }

                if (!is_dir($file_name)) {
                    $fileSize = zip_entry_filesize($dirResource);
                    $file_content = zip_entry_read($dirResource, $fileSize);
                    file_put_contents($file_name, $file_content);
                }
                zip_entry_close($dirResource);
            }
        }
        zip_close($resource);
    }

    public static function download($url)
    {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_SSLVERSION,3);
        $data = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        return [$data, $error];
    }
}