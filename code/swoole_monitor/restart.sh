#!/bin/bash

ps aux|grep monitorServer.php |grep -v grep |awk '{print $2}'|xargs kill -9
php monitorServer.php