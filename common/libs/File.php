<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-10-16 17:15
 */

namespace common\libs;

class File
{

    private $_files;
    private $error = '';
    private $config = [
        'maxSize' => 90000000000,
        'allowTypes' => ['image/png', 'image/jpg', 'image/jpeg'],
    ];
    private $path = '';
    public $uploadedFiles = [];

    public function __construct(array $config = [])
    {
        $this->_files = $_FILES;
        if (isset($config['maxSize'])) {
            $this->config['maxSize'] = $config['maxSize'];
        }
        if (isset($config['allowTypes'])) {
            $this->config['allowTypes'] = $config['allowTypes'];
        }
    }

    public function upload($path = '', $name = '')
    {
        if (! empty($path)) {
            $this->path = $path;
        } else {
            $this->path = $_SERVER["DOCUMENT_ROOT"] . '/img';
        }
        $sign = key($this->_files);
        if (! isset($this->_files)) {
            $this->error = "上传的文件不存在";
            return false;
        }
        if (! isset($this->_files[$sign]['name'])) {
            return false;
        }
        if (is_array($this->_files[$sign]['name'])) {
            foreach ($this->_files[$sign]['name'] as $num => $value) {
                $nameNew = $name;
                if (is_array($name)) {//var_dump($name);die;
                    $nameNew = isset($name[$num]) ? $name[$num] : '';
                }
                $this->uploadedFiles[] = $this->_uploadOne($nameNew, $sign, $num);
            }
        } else {
            if (is_array($name)) {
                $name = $name[0];
            }
            $this->uploadedFiles = $this->_uploadOne($name, $sign);
            if ($this->uploadedFiles === false) {
                return false;
            }
        }
        return $this->uploadedFiles;
    }

    private function _uploadOne($name, $sign, $num = false)
    {
        if ($num !== false) {//var_dump($this->_files[$sign]);die;
            $fileSize = $this->_files[$sign]['size'][$num];
            $fileType = $this->_files[$sign]['type'][$num];
            $temp = pathinfo($this->_files[$sign]['name'][$num]);
            $tmp_name = $this->_files[$sign]['tmp_name'][$num];
        } else {
            $fileSize = $this->_files[$sign]['size'];
            $fileType = $this->_files[$sign]['type'];
            $temp = pathinfo($this->_files[$sign]['name']);
            $tmp_name = $this->_files[$sign]['tmp_name'];
        }
        if ($fileSize > $this->config['maxSize']) {
            $this->error = "文件大小超过最大允许上传值";
            return false;
        }
        if (! in_array($fileType, $this->config['allowTypes'])) {
            $this->error = "不允许上传的文件类型";
            return false;
        }
        $extension = '';
        if (isset($temp['extension'])) {
            $extension = $temp['extension'];
        }
        if (empty($name)) {
            $name = date('YmdHis') . rand(0, 100) . '.' . $temp['extension'];
        } else {
            $custom = pathinfo($name);
            if (! isset($custom['extension']) && stripos($name, '.' . $extension) === false) {
                $name .= '.' . $extension;
            }
        }
        if (! is_dir($this->path)) {
            if (! $this->mk_dir($this->path)) {
                $this->error = "创建文件夹失败";
                return false;
            }
        }
        if (substr($this->path, -1) != '/') {
            $this->path .= '/';
        }
        $destination = $this->path . $name;
        $res = move_uploaded_file($tmp_name, $destination);
        if ($res) {
            return $destination;
        }
        $this->error = "保存文件失败";
        return false;


    }

    public function getErrors()
    {
        return $this->error;
    }

    private function mk_dir($dir, $mod = '0777')
    {
        if (! is_readable($dir)) {
            try {
                $this->mk_dir(dirname($dir));
                if (! is_file($dir)) {
                    mkdir($dir, $mod);
                    chmod($dir, 0777);
                }
            } catch (Exeption $e) {
                return false;
            }
        }
        return true;
    }
}