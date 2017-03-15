<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-11 05:39
 */

namespace common\helpers;

class StringHelper extends \yii\helpers\StringHelper
{

    public static function mk_dir($dir, $mod = '0777')
    {
        if (! is_readable($dir)) {
            try {
                self::mk_dir(dirname($dir));
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

    public static function deleteDir($dir)
    {
        $directory = @opendir($dir);
        while ($file = @readdir($directory)) {
            if ($file != '.' && $file != '..') {
                $fullPath = $dir . DIRECTORY_SEPARATOR . $file;
                if (! is_dir($fullPath)) {
                    @unlink($fullPath);
                } else {
                    static::deleteDir($fullPath);
                }

            }
        }
    }

    public static function getDirSize($dir)
    {
        $handle = opendir($dir);
        $sizeResult = 0;
        while (false !== ($FolderOrFile = readdir($handle))) {
            if ($FolderOrFile != "." && $FolderOrFile != "..") {
                if (is_dir("$dir/$FolderOrFile")) {
                    $sizeResult += self::getDirSize("$dir/$FolderOrFile");
                } else {
                    $sizeResult += filesize("$dir/$FolderOrFile");
                }
            }
        }
        closedir($handle);
        return $sizeResult;
    }

    public static function truncate_utf8_string($string, $length, $etc = '...')
    {
        $result = '';
        $string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'UTF-8');
        $strlen = strlen($string);
        for ($i = 0; (($i < $strlen) && ($length > 0)); $i++) {
            if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0')) {
                if ($length < 1.0) {
                    break;
                }
                $result .= substr($string, $i, $number);
                $length -= 1.0;
                $i += $number - 1;
            } else {
                $result .= substr($string, $i, 1);
                $length -= 0.5;
            }
        }
        $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
        if ($i < $strlen) {
            $result .= $etc;
        }
        return $result;
    }

    public static function tranTime($time)
    {
        $rtime = '';//date("m-d H:i",$time);
        $htime = '';//date("H:i",$time);

        $time = time() - $time;

        if ($time < 60) {
            $str = '刚刚';
        } elseif ($time < 60 * 60) {
            $min = floor($time / 60);
            $str = $min . '分钟前';
        } elseif ($time < 60 * 60 * 24) {
            $h = floor($time / (60 * 60));
            $str = $h . '小时前 ' . $htime;
        } elseif ($time < 60 * 60 * 24 * 3) {
            $d = floor($time / (60 * 60 * 24));
            if ($d == 1) {
                $str = '昨天 ' . $rtime;
            } else {
                $str = '前天 ' . $rtime;
            }
        } else {
            $str = $rtime;
        }
        return $str;
    }

    public static function utf8Encoding($in_str)
    {
        $cur_encoding = mb_detect_encoding($in_str);
        if ($cur_encoding == "UTF-8" && mb_check_encoding($in_str, "UTF-8")) {
            return $in_str;
        } else {
            return utf8_encode($in_str);
        }
    }

    public static function getServerStatus()
    {
        switch (PHP_OS) {

            case "Linux":

                $sysReShow = (false !== ($sysInfo = self::sys_linux())) ? "show" : "none";

                break;


            case "FreeBSD":

                $sysReShow = (false !== ($sysInfo = self::sys_freebsd())) ? "show" : "none";

                break;


            /*case "WINNT":

                $sysReShow = (false !== ($sysInfo = self::sys_windows()))?"show":"none";

            break;*/


            default:

                break;

        }
    }

    //linux系统探测

    private static function sys_linux()

    {

        // CPU

        if (false === ($str = @file("/proc/cpuinfo"))) {
            return false;
        }

        $str = implode("", $str);

        @preg_match_all("/model\s+name\s{0,}\:+\s{0,}([\w\s\)\(\@.-]+)([\r\n]+)/s", $str, $model);

        @preg_match_all("/cpu\s+MHz\s{0,}\:+\s{0,}([\d\.]+)[\r\n]+/", $str, $mhz);

        @preg_match_all("/cache\s+size\s{0,}\:+\s{0,}([\d\.]+\s{0,}[A-Z]+[\r\n]+)/", $str, $cache);

        @preg_match_all("/bogomips\s{0,}\:+\s{0,}([\d\.]+)[\r\n]+/", $str, $bogomips);

        if (false !== is_array($model[1])) {

            $res['cpu']['num'] = sizeof($model[1]);
            /*

            for($i = 0; $i < $res['cpu']['num']; $i++)

            {

                $res['cpu']['model'][] = $model[1][$i].'&nbsp;('.$mhz[1][$i].')';

                $res['cpu']['mhz'][] = $mhz[1][$i];

                $res['cpu']['cache'][] = $cache[1][$i];

                $res['cpu']['bogomips'][] = $bogomips[1][$i];

            }*/
            if ($res['cpu']['num'] == 1) {
                $x1 = '';
            } else {
                $x1 = ' ×' . $res['cpu']['num'];
            }
            $mhz[1][0] = ' | 频率:' . $mhz[1][0];
            $cache[1][0] = ' | 二级缓存:' . $cache[1][0];
            $bogomips[1][0] = ' | Bogomips:' . $bogomips[1][0];
            $res['cpu']['model'][] = $model[1][0] . $mhz[1][0] . $cache[1][0] . $bogomips[1][0] . $x1;

            if (false !== is_array($res['cpu']['model'])) {
                $res['cpu']['model'] = implode("<br />", $res['cpu']['model']);
            }

            if (false !== is_array($res['cpu']['mhz'])) {
                $res['cpu']['mhz'] = implode("<br />", $res['cpu']['mhz']);
            }

            if (false !== is_array($res['cpu']['cache'])) {
                $res['cpu']['cache'] = implode("<br />", $res['cpu']['cache']);
            }

            if (false !== is_array($res['cpu']['bogomips'])) {
                $res['cpu']['bogomips'] = implode("<br />", $res['cpu']['bogomips']);
            }

        }


        // NETWORK


        // UPTIME

        if (false === ($str = @file("/proc/uptime"))) {
            return false;
        }

        $str = explode(" ", implode("", $str));

        $str = trim($str[0]);

        $min = $str / 60;

        $hours = $min / 60;

        $days = floor($hours / 24);

        $hours = floor($hours - ($days * 24));

        $min = floor($min - ($days * 60 * 24) - ($hours * 60));

        if ($days !== 0) {
            $res['uptime'] = $days . "天";
        }

        if ($hours !== 0) {
            $res['uptime'] .= $hours . "小时";
        }

        $res['uptime'] .= $min . "分钟";


        // MEMORY

        if (false === ($str = @file("/proc/meminfo"))) {
            return false;
        }

        $str = implode("", $str);

        preg_match_all("/MemTotal\s{0,}\:+\s{0,}([\d\.]+).+?MemFree\s{0,}\:+\s{0,}([\d\.]+).+?Cached\s{0,}\:+\s{0,}([\d\.]+).+?SwapTotal\s{0,}\:+\s{0,}([\d\.]+).+?SwapFree\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $buf);
        preg_match_all("/Buffers\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $buffers);


        $res['memTotal'] = round($buf[1][0] / 1024, 2);

        $res['memFree'] = round($buf[2][0] / 1024, 2);

        $res['memBuffers'] = round($buffers[1][0] / 1024, 2);
        $res['memCached'] = round($buf[3][0] / 1024, 2);

        $res['memUsed'] = $res['memTotal'] - $res['memFree'];

        $res['memPercent'] = (floatval($res['memTotal']) != 0) ? round($res['memUsed'] / $res['memTotal'] * 100, 2) : 0;


        $res['memRealUsed'] = $res['memTotal'] - $res['memFree'] - $res['memCached'] - $res['memBuffers']; //真实内存使用
        $res['memRealFree'] = $res['memTotal'] - $res['memRealUsed']; //真实空闲
        $res['memRealPercent'] = (floatval($res['memTotal']) != 0) ? round($res['memRealUsed'] / $res['memTotal'] * 100, 2) : 0; //真实内存使用率

        $res['memCachedPercent'] = (floatval($res['memCached']) != 0) ? round($res['memCached'] / $res['memTotal'] * 100, 2) : 0; //Cached内存使用率

        $res['swapTotal'] = round($buf[4][0] / 1024, 2);

        $res['swapFree'] = round($buf[5][0] / 1024, 2);

        $res['swapUsed'] = round($res['swapTotal'] - $res['swapFree'], 2);

        $res['swapPercent'] = (floatval($res['swapTotal']) != 0) ? round($res['swapUsed'] / $res['swapTotal'] * 100, 2) : 0;


        // LOAD AVG

        if (false === ($str = @file("/proc/loadavg"))) {
            return false;
        }

        $str = explode(" ", implode("", $str));

        $str = array_chunk($str, 4);

        $res['loadAvg'] = implode(" ", $str[0]);


        return $res;

    }


    //FreeBSD系统探测

    private static function sys_freebsd()
    {

        //CPU

        if (false === ($res['cpu']['num'] = get_key("hw.ncpu"))) {
            return false;
        }

        $res['cpu']['model'] = get_key("hw.model");

        //LOAD AVG

        if (false === ($res['loadAvg'] = get_key("vm.loadavg"))) {
            return false;
        }

        //UPTIME

        if (false === ($buf = get_key("kern.boottime"))) {
            return false;
        }

        $buf = explode(' ', $buf);

        $sys_ticks = time() - intval($buf[3]);

        $min = $sys_ticks / 60;

        $hours = $min / 60;

        $days = floor($hours / 24);

        $hours = floor($hours - ($days * 24));

        $min = floor($min - ($days * 60 * 24) - ($hours * 60));

        if ($days !== 0) {
            $res['uptime'] = $days . "天";
        }

        if ($hours !== 0) {
            $res['uptime'] .= $hours . "小时";
        }

        $res['uptime'] .= $min . "分钟";

        //MEMORY

        if (false === ($buf = get_key("hw.physmem"))) {
            return false;
        }

        $res['memTotal'] = round($buf / 1024 / 1024, 2);


        $str = get_key("vm.vmtotal");

        preg_match_all("/\nVirtual Memory[\:\s]*\(Total[\:\s]*([\d]+)K[\,\s]*Active[\:\s]*([\d]+)K\)\n/i", $str, $buff, PREG_SET_ORDER);

        preg_match_all("/\nReal Memory[\:\s]*\(Total[\:\s]*([\d]+)K[\,\s]*Active[\:\s]*([\d]+)K\)\n/i", $str, $buf, PREG_SET_ORDER);


        $res['memRealUsed'] = round($buf[0][2] / 1024, 2);

        $res['memCached'] = round($buff[0][2] / 1024, 2);

        $res['memUsed'] = round($buf[0][1] / 1024, 2) + $res['memCached'];

        $res['memFree'] = $res['memTotal'] - $res['memUsed'];

        $res['memPercent'] = (floatval($res['memTotal']) != 0) ? round($res['memUsed'] / $res['memTotal'] * 100, 2) : 0;


        $res['memRealPercent'] = (floatval($res['memTotal']) != 0) ? round($res['memRealUsed'] / $res['memTotal'] * 100, 2) : 0;


        return $res;

    }

    private static function sys_windows()
    {

        if (PHP_VERSION >= 5) {

            $objLocator = new COM("WbemScripting.SWbemLocator");

            $wmi = $objLocator->ConnectServer();

            $prop = $wmi->get("Win32_PnPEntity");

        } else {
            return false;

        }


        //CPU

        $cpuinfo = GetWMI($wmi, "Win32_Processor", array("Name", "L2CacheSize", "NumberOfCores"));

        $res['cpu']['num'] = $cpuinfo[0]['NumberOfCores'];

        if (null == $res['cpu']['num']) {

            $res['cpu']['num'] = 1;

        }/*

	for ($i=0;$i<$res['cpu']['num'];$i++)
	{

		$res['cpu']['model'] .= $cpuinfo[0]['Name']."<br />";

		$res['cpu']['cache'] .= $cpuinfo[0]['L2CacheSize']."<br />";

	}*/
        $cpuinfo[0]['L2CacheSize'] = ' (' . $cpuinfo[0]['L2CacheSize'] . ')';
        if ($res['cpu']['num'] == 1) {
            $x1 = '';
        } else {
            $x1 = ' ×' . $res['cpu']['num'];
        }
        $res['cpu']['model'] = $cpuinfo[0]['Name'] . $cpuinfo[0]['L2CacheSize'] . $x1;

        // SYSINFO

        $sysinfo = GetWMI($wmi, "Win32_OperatingSystem", array(
            'LastBootUpTime',
            'TotalVisibleMemorySize',
            'FreePhysicalMemory',
            'Caption',
            'CSDVersion',
            'SerialNumber',
            'InstallDate'
        ));

        $sysinfo[0]['Caption'] = iconv('GBK', 'UTF-8', $sysinfo[0]['Caption']);

        $sysinfo[0]['CSDVersion'] = iconv('GBK', 'UTF-8', $sysinfo[0]['CSDVersion']);

        $res['win_n'] = $sysinfo[0]['Caption'] . " " . $sysinfo[0]['CSDVersion'] . " 序列号:{$sysinfo[0]['SerialNumber']} 于" . date('Y年m月d日H:i:s', strtotime(substr($sysinfo[0]['InstallDate'], 0, 14))) . "安装";

        //UPTIME

        $res['uptime'] = $sysinfo[0]['LastBootUpTime'];


        $sys_ticks = 3600 * 8 + time() - strtotime(substr($res['uptime'], 0, 14));

        $min = $sys_ticks / 60;

        $hours = $min / 60;

        $days = floor($hours / 24);

        $hours = floor($hours - ($days * 24));

        $min = floor($min - ($days * 60 * 24) - ($hours * 60));

        if ($days !== 0) {
            $res['uptime'] = $days . "天";
        }

        if ($hours !== 0) {
            $res['uptime'] .= $hours . "小时";
        }

        $res['uptime'] .= $min . "分钟";


        //MEMORY

        $res['memTotal'] = round($sysinfo[0]['TotalVisibleMemorySize'] / 1024, 2);

        $res['memFree'] = round($sysinfo[0]['FreePhysicalMemory'] / 1024, 2);

        $res['memUsed'] = $res['memTotal'] - $res['memFree'];    //上面两行已经除以1024,这行不用再除了

        $res['memPercent'] = round($res['memUsed'] / $res['memTotal'] * 100, 2);


        $swapinfo = GetWMI($wmi, "Win32_PageFileUsage", array('AllocatedBaseSize', 'CurrentUsage'));


        // LoadPercentage

        $loadinfo = GetWMI($wmi, "Win32_Processor", array("LoadPercentage"));

        $res['loadAvg'] = $loadinfo[0]['LoadPercentage'];


        return $res;

    }

    public static function formatBytes($size)
    {
        $units = array(' B', ' KB', ' MB', ' GB', ' TB');
        for ($i = 0; $size >= 1024 && $i < 4; $i++) {
            $size /= 1024;
        }
        return round($size, 2) . $units[$i];
    }

    public static function path_info($filepath)
    {
        $path_parts = array();
        $path_parts ['dirname'] = rtrim(substr($filepath, 0, strrpos($filepath, '/')), "/") . "/";
        $path_parts ['basename'] = ltrim(substr($filepath, strrpos($filepath, '/')), "/");
        $path_parts ['extension'] = substr(strrchr($filepath, '.'), 1);
        $path_parts ['filename'] = ltrim(substr($path_parts ['basename'], 0, strrpos($path_parts ['basename'], '.')), "/");
        return $path_parts;
    }
}