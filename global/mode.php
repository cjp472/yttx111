<?php

/**
 * 全局运行模式设置文件 [重要]
 * 
 * < 为了解决在线版online和体验版代码合并问题，我们定义了公用的全局通用变量来区分! >
 * @date 2015/07/29
 */

/**
 * 系统运行环境
 *
 * 体验模式 = experience
 * 正常模式 = normal
 */
define('DHB_RUNTIME_MODE', 'normal');

/**
 * 开发者提供的调试
 *
 * 开发模式 = development
 * 线上模式 = online
 */
define('DHB_DEVELOPMENT_MODE', 'development');

?>