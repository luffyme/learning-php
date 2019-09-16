<?php 

class RedisScan {
    public function work()
    {
        $this->stringScan();
        $this->hashScan();
        $this->zSetScan();
    }

    private function zSetScan()
    {
        $redis = $this->newRedis();
        $keyName = "user_gray_version";
        $pattern = '*';
        $i = NULL;
        $count = 10000;

        do
        {
            $keysArr = $redis->zscan($keyName, $it, $pattern, $count);
            if (!empty($keysArr)) {
                foreach ($keysArr as $key) {
                    echo "{$key}\n";
                }
                unset($keysArr);
            }

        } while ($i > 0);

        $redis->close();
    }

    private function hashScan()
    {
        $redis = $this->newRedis();
        $keyName = "user_gray_version";
        $pattern = '*';
        $i = NULL;
        $count = 10000;

        do
        {
            $keysArr = $redis->hscan($keyName, $it, $pattern, $count);
            if (!empty($keysArr)) {
                foreach ($keysArr as $key) {
                    echo "{$key}\n";
                }
                unset($keysArr);
            }

        } while ($i > 0);

        $redis->close();
    }

    private function stringScan()
    {
        $redis = $this->newRedis();
        $pattern = "user_gray_version_*";
        $i = NULL;
        $count = 10000;

        do
        {
            $keysArr = $redis->scan($i, $pattern, $count);
            if (!empty($keysArr)) {
                foreach ($keysArr as $key) {
                    echo "{$key}\n";
                }
                unset($keysArr);
            }

        } while ($i > 0);

        $redis->close();
    }

    private function newRedis()
    {
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379, 0.5);
        $redis->auth('123456');

        return $redis;
    }
}

$redisScan = new RedisScan();
$redisScan->work();