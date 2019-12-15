<?php

$lua = <<<EOF 

local num = redis.call('GET', KEYS[1]);

if not num then
	return 0
else 
	local res = num * ARGV[1];
	redis.call('SET', KEYS[1], res)
	return res;
end

EOF;

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$ret = $redis->eval($lua, array('lua:test', 2), 1)
var_dump($ret);
//eval函数的第3个参数为KEYS个数。 phpredis依据此值将KEYS和ARGV做区分。
