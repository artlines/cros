<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/setup.html#checking-symfony-application-configuration-and-setup
// for more information
//umask(0000);

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
if (!(in_array(@$_SERVER['REMOTE_ADDR'], array('192.168.5.22', '195.211.31.245', '176.215.13.125', '172.31.74.224', '172.31.74.109', '127.0.0.1', '::1', '94.230.139.2', '87.251.176.34', '82.112.32.85', '5.189.43.55', '192.168.5.82', '5.135.190.34', '192.168.5.212','172.31.74.101', '195.211.31.240')))
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information. '.$_SERVER['REMOTE_ADDR']);
}

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require __DIR__.'/../app/autoload.php';
Debug::enable();

$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
