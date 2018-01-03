<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-10-16 17:15
 */

namespace common\libs;

class ServerInfo
{
    /**
     * 获取服务器实时信息，支持不同操作系统
     *
     */
    public function getinfo()
    {
        error_reporting(0);    //会有多处报错，因此这里进行屏蔽
        switch (PHP_OS) {// 根据不同系统取得CPU相关信息
            case "Linux":
                $sysInfo = self::sys_linux();
                break;
            case "FreeBSD":
                $sysInfo = self::sys_freebsd();
                break;
            default:
                $sysInfo = [];
                break;
        }
        $result = $sysInfo;
        $result['freeSpace'] = round(@disk_free_space(".") / (1024 * 1024 * 1024), 3);
        $result['diskTotal'] = round(@disk_total_space(".") / (1024 * 1024 * 1024), 3);    //总空间
        //网卡流量
        $strs = @file("/proc/net/dev");
        $NetOut = array();
        for ($i = 2; $i < count($strs); $i++) {
            preg_match_all("/([^\s]+):[\s]{0,}(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/", $strs[$i], $info);
            /*	$NetInput[$i] = formatsize($info[2][0]);
                $NetOut[$i]  = formatsize($info[10][0]);
            */
            $tmo = round($info[2][0] / 1024 / 1024, 5);
            $tmo2 = round($tmo / 1024, 5);
            $NetInput[$i] = $tmo2;
            $tmp = round($info[10][0] / 1024 / 1024, 5);
            $tmp2 = round($tmp / 1024, 5);
            $NetOut[$i] = $tmp2;
        }
        //判断内存如果小于1GB，就显示M，否则显示GB单位
        if ($sysInfo['memTotal'] < 1024) {
            $result['TotalMemory'] = $sysInfo['memTotal'] . " MB";
            $result['UsedMemory'] = $sysInfo['memUsed'] . " MB";
            $result['FreeMemory'] = $sysInfo['memFree'] . " MB";
            $result['CachedMemory'] = $sysInfo['memCached'] . " MB";    //cache化内存
            $result['Buffers'] = $sysInfo['memBuffers'] . " MB";    //缓冲
            $result['TotalSwap'] = $sysInfo['swapTotal'] . " MB";
            $result['swapUsed'] = $sysInfo['swapUsed'] . " MB";
            $result['swapFree'] = $sysInfo['swapFree'] . " MB";
            $result['swapPercent'] = $sysInfo['swapPercent'];
            $result['memRealUsed'] = $sysInfo['memRealUsed'] . " MB"; //真实内存使用
            $result['memRealFree'] = $sysInfo['memRealFree'] . " MB"; //真实内存空闲
            $result['memRealPercent'] = $sysInfo['memRealPercent']; //真实内存使用比率
            $result['memPercent'] = $sysInfo['memPercent'] . '%'; //内存总使用率
            $result['memCachedPercent'] = $sysInfo['memCachedPercent']; //cache内存使用率
        } else {
            $result['TotalMemory'] = round($sysInfo['memTotal'] / 1024, 3) . " GB";
            $result['UsedMemory'] = round($sysInfo['memUsed'] / 1024, 3) . " GB";
            $result['FreeMemory'] = round($sysInfo['memFree'] / 1024, 3) . " GB";
            $result['CachedMemory'] = round($sysInfo['memCached'] / 1024, 3) . " GB";
            $result['Buffers'] = round($sysInfo['memBuffers'] / 1024, 3) . " GB";
            $result['TotalSwap'] = round($sysInfo['swapTotal'] / 1024, 3) . " GB";
            $result['swapUsed'] = round($sysInfo['swapUsed'] / 1024, 3) . " GB";
            $result['swapFree'] = round($sysInfo['swapFree'] / 1024, 3) . " GB";
            $result['swapPercent'] = $sysInfo['swapPercent'];
            $result['memRealUsed'] = round($sysInfo['memRealUsed'] / 1024, 3) . " GB"; //真实内存使用
            $result['memRealFree'] = round($sysInfo['memRealFree'] / 1024, 3) . " GB"; //真实内存空闲
            $result['memRealPercent'] = $sysInfo['memRealPercent']; //真实内存使用比率
            $result['memPercent'] = $sysInfo['memPercent'] . '%'; //内存总使用率
            $result['memCachedPercent'] = $sysInfo['memCachedPercent']; //cache内存使用率
        }
        $result['barmemCachedPercent'] = $result['memCachedPercent'] . '%';
        $result['barswapPercent'] = $result['swapPercent'] . '%';
        $result['barmemRealPercent'] = $result['memRealPercent'] . '%';
        $result['NetOut2'] = $NetOut[2];
        $result['NetOut3'] = $NetOut[3];
        $result['NetOut4'] = $NetOut[4];
        $result['NetOut5'] = $NetOut[5];
        $result['NetOut6'] = $NetOut[6];
        $result['NetOut7'] = $NetOut[7];
        $result['NetOut8'] = $NetOut[8];
        $result['NetOut9'] = $NetOut[9];
        $result['NetOut10'] = $NetOut[10];
        $result['NetInput2'] = $NetInput[2];
        $result['NetInput3'] = $NetInput[3];
        $result['NetInput4'] = $NetInput[4];
        $result['NetInput5'] = $NetInput[5];
        $result['NetInput6'] = $NetInput[6];
        $result['NetInput7'] = $NetInput[7];
        $result['NetInput8'] = $NetInput[8];
        $result['NetInput9'] = $NetInput[9];
        $result['NetInput10'] = $NetInput[10];

        return $result;
    }

    //linux系统探测
    public function sys_linux()
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
            for($i = 0; $i < $res['cpu']['num']; $i++){
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
    public function sys_freebsd()
    {
        //CPU
        if (false === ($res['cpu']['num'] = self::get_key("hw.ncpu"))) {
            return false;
        }
        $res['cpu']['model'] = self::get_key("hw.model");
        //LOAD AVG
        if (false === ($res['loadAvg'] = self::get_key("vm.loadavg"))) {
            return false;
        }
        //UPTIME
        if (false === ($buf = self::get_key("kern.boottime"))) {
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
        if (false === ($buf = self::get_key("hw.physmem"))) {
            return false;
        }
        $res['memTotal'] = round($buf / 1024 / 1024, 2);

        $str = self::get_key("vm.vmtotal");
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

    //取得参数值 FreeBSD
    private function get_key($keyName)
    {
        return self::do_command('sysctl', "-n $keyName");
    }

    //确定执行文件位置 FreeBSD
    private function find_command($commandName)
    {
        $path = array('/bin', '/sbin', '/usr/bin', '/usr/sbin', '/usr/local/bin', '/usr/local/sbin');
        foreach ($path as $p) {
            if (@is_executable("$p/$commandName")) {
                return "$p/$commandName";
            }
        }
        return false;
    }

    //执行系统命令 FreeBSD
    private function do_command($commandName, $args)
    {
        $buffer = "";
        if (false === ($command = self::find_command($commandName))) {
            return false;
        }
        if ($fp = @popen("$command $args", 'r')) {
            while (! @feof($fp)) {
                $buffer .= @fgets($fp, 4096);
            }
            return trim($buffer);
        }
        return false;
    }

    private function GetWMI($wmi, $strClass, $strValue = array())
    {
        $arrData = array();

        $objWEBM = $wmi->Get($strClass);
        $arrProp = $objWEBM->Properties_;
        $arrWEBMCol = $objWEBM->Instances_();
        foreach ($arrWEBMCol as $objItem) {
            @reset($arrProp);
            $arrInstance = array();
            foreach ($arrProp as $propItem) {
                eval("\$value = \$objItem->" . $propItem->Name . ";");
                if (empty($strValue)) {
                    $arrInstance[$propItem->Name] = trim($value);
                } else {
                    if (in_array($propItem->Name, $strValue)) {
                        $arrInstance[$propItem->Name] = trim($value);
                    }
                }
            }
            $arrData[] = $arrInstance;
        }
        return $arrData;
    }
}