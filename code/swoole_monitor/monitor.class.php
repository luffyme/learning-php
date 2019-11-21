<?php
date_default_timezone_set("Asia/Chongqing");
class monitor
{
    public static function syn($params)
    {
        $synRecv = shell_exec("ss -nat | grep SYN-RECV | wc -l");
        $synSent = shell_exec("ss -nat | grep SYN-SENT | wc -l");
        $data = array('syn' => trim($synRecv), 'syn_recv' => trim($synRecv), 'syn_sent' => trim($synSent));
        return $data;
    }

    public static function load($params)
    {
        $cpuCount = shell_exec("grep -c 'model name' /proc/cpuinfo");
        $cpuCount = trim($cpuCount);
        $load = shell_exec("cat /proc/loadavg | awk '{print $1}'");
        $data = array('load_avg' => trim($load), 'cpu_count' => trim($cpuCount));
        return $data;
    }

    public static function cpu($params)
    {
        exec("vmstat 1 4 |awk '{print $15}'", $arr);
        $n = count($arr) - 1;
        if ($n > 3) {
            $k = 0;
            $total = 0;
            for ($i = 0; $i < $n; $i++) {
                if ($i < 3) {
                    $total = $total + $arr[$n - $i];
                    $k++;
                }
            }
            $cpu = round($total / $k, 2);
        } else {
            $cpu = 100;
        }

        $data = array('cpu' => $cpu, 'type' => 'vmstat');
        return $data;
    }


    public static function cpu0($params)
    {
        $command = "cat /proc/stat | grep 'cpu0'";
        $cpu0 = shell_exec($command);
        $cpu0 = trim($cpu0);
        $array = explode(" ", $cpu0);

        $cpuTotalNew = $array[1] + $array[2] + $array[3] + $array[4] + $array[5] + $array[6] + $array[7];
        $cpuUsedNew = $array[1] + $array[2] + $array[3] + $array[6] + $array[7];

        $cpuTotalOld = file_exists('/tmp/cpu0Total') ? file_get_contents('/tmp/cpu0Total') : 0;
        $cpuUsedOld = file_exists('/tmp/cpu0Used') ? file_get_contents('/tmp/cpu0Used') : 0;


        file_put_contents('/tmp/cpu0Total', $cpuTotalNew);
        file_put_contents('/tmp/cpu0Used', $cpuUsedNew);

        $data = array('cpu0' => ($cpuUsedNew - $cpuUsedOld) / ($cpuTotalNew - $cpuTotalOld) * 100);

        return $data;
    }

    public static function mem($params)
    {
        $memInfo = shell_exec("/usr/bin/free -m|grep Mem|awk '{print $2\"##\"$4}'");
        $mem = explode("##", $memInfo);
        $data = array('mem' => trim($mem[1]), 'total' => trim($mem[0]));
        return $data;
    }

    public static function memv2($params)
    {
        $memInfo = shell_exec("cat /proc/meminfo |grep MemTotal");
        $tmpArr = explode(":", $memInfo);
        $total = intval(trim($tmpArr[1]));

        $memInfo = shell_exec("cat /proc/meminfo |grep MemFree");
        $tmpArr = explode(":", $memInfo);
        $free = intval(trim($tmpArr[1]));

        $memInfo = shell_exec("cat /proc/meminfo |grep Buffers");
        $tmpArr = explode(":", $memInfo);
        $buffers = intval(trim($tmpArr[1]));

        $memInfo = shell_exec("cat /proc/meminfo |grep Cached");
        $tmpArr = explode(":", $memInfo);
        $cached = intval(trim($tmpArr[1]));

        $available = $free + $buffers + $cached;
        $used = $total - $available;
        $data = array('used' => $used, 'total' => $total, 'free' => $free, 'buffers' => $buffers, 'cached' => $cached);
        return $data;
    }

    public static function swap($params)
    {
        $swap = shell_exec("/usr/bin/free -m|grep Swap|awk '{print $3}'");
        $data = array('swap' => trim($swap));
        return $data;
    }

    public static function disk($params)
    {
        if ($params == 'string') {
            $data = shell_exec("df -h | sed 1d");
        } else {
            $res = shell_exec('df -h | sed 1d|awk \'{print $2"|"$5"|"$6"##"}\'');
            $resArr = explode('##', $res);
            $data = array();
            for ($i = 0; $i < count($resArr); $i++) {
                $infoArr = explode('|', trim($resArr[$i]));
                $data[$i]['total'] = $infoArr[0];
                $data[$i]['use'] = $infoArr[1];
                $data[$i]['partition'] = $infoArr[2];
            }
        }

        return $data;
    }

    public static function httpd($params)
    {
        $http = shell_exec("ps axu|grep httpd|grep -v grep |wc -l");
        $data = array('httpd' => trim($http));
        return $data;
    }

    public static function nginx($params)
    {
        $nginx = shell_exec("ps axu|grep nginx|grep -v grep |wc -l");
        $data = array('nginx' => trim($nginx));
        return $data;
    }

    //检查端口是否存在
    public static function CheckPortExist($params)
    {
        $port = $params;
        $command = "ss -nat | grep :{$port} | wc -l";
        $count = shell_exec($command);

        $data = array('ret' => ($count >= 1) ? 2 : 1);
        return $data;
    }
}