<?php
include 'monitor.class.php';

//$ip = shell_exec('ifconfig eth1|grep inet|awk \'{print $2}\'|tr -d "addr:"');
$ip = '0.0.0.0';
$GLOBALS['ip'] = trim($ip);
$serv = new Swoole_Http_Server($GLOBALS['ip'], 9999);
$serv->set(array(
    'worker_num' => 8,   //工作进程数量
    'daemonize' => true, //是否作为守护进程
));
$serv->on('Request', function ($request, $response) use ($serv) {

    $ipAllowList = array();
    $remoteAddr = $request->server['remote_addr'];

    if (in_array($remoteAddr, $ipAllowList)) {
        

        $action = isset($request->get['action'])?$request->get['action']:'';
        $params = isset($request->get['params'])?$request->get['params']:'';

        //兼容POST形式
        if (empty($action)) {
            $action = isset($request->post['action'])?$request->post['action']:'';
            $params = isset($request->post['params'])?$request->post['params']:'';
        }

        if ($action == 'monitor_relaod') {
            $serv->reload();
            $res = array('ret' => 2);
        }

        if (method_exists(new monitor, $action)) {
            $res = call_user_func("monitor::{$action}", $params);
        }

        $res['ip'] = $GLOBALS['ip'];
        $response->cookie("User", "Swoole");
        $response->header("X-Server", "Swoole");
        $response->end(json_encode($res));

    } else {
        $res = array(
            'code' => 0,
            'msg'  => "{$remoteAddr} - not allow!"
        );
        $response->end(json_encode($res));
    }
});

$serv->start();