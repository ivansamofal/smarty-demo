<?php

declare(strict_types=1);

use App\Config\Config;
use App\View\SmartyFactory;

require dirname(__DIR__) . '/vendor/autoload.php';

$smarty = SmartyFactory::create();
$smarty->assign('appName', Config::get('APP_ENV') === 'dev' ? 'Smarty Demo Blog (dev)' : 'Smarty Demo Blog');
$smarty->assign('phpVersion', PHP_VERSION);
$smarty->assign('smartyVersion', \Smarty::SMARTY_VERSION);
$smarty->display('hello.tpl');
