<?php

$params = array('username' => 'luffyme', 'password' => 'password');
$sign = createSign($params);
echo "{$sign}\n";

function createSign($params)
{
    $strSalt = '1scv6zfzSR1wLaWN';
    $strVal  = '';
    if (!empty($params)) {
        if (isset($params['sign'])) {
            unset($params['sign']);
        }

        ksort($params);
        $strVal = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }
    return md5(md5($strSalt).md5($strVal));
}