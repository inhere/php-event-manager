<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-12-07
 * Time: 14:07
 */

use Inhere\Event\EventManager;
use Inhere\Event\Examples\App;
use Inhere\Event\Examples\AppListener;

require dirname(__DIR__) . '/tests/boot.php';

$em = new EventManager();

// register a group listener
$em->attach('app', new AppListener());

// create app
$app = new App($em);

// run.
$app->run();