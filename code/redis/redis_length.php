<?php 

//统计Redis中key的大小
//因为 DEBUG 返回的 serializedlength 是序列化后的长度，所以最终计算的值小于实际内存占用，但考虑到相对大小依然是有参考意义的。


$patterns = array(
    'foo:.+',
    'bar:.+',
    '.+',
);

$redis = new Redis();
$redis->setOption(Redis::OPT_SCAN, Redis::SCAN_RETRY);

$result = array_fill_keys($patterns, 0);

while ($keys = $redis->scan($it, $match = '*', $count = 1000)) {
    foreach ($keys as $key) {
        foreach ($patterns as $pattern) {
            if (preg_match("/^{$pattern}$/", $key)) {
                if ($v = $redis->debug($key)) {
                    $result[$pattern] += $v['serializedlength'];
                }

                break;
            }
        }
    }
}

var_dump($result);
