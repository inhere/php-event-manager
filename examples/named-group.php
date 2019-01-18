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

require dirname(__DIR__) . '/test/boot.php';

$em = new EventManager();

$groupListener = new AppListener();

// register a group listener
$em->attach('app', $groupListener);

// all `app.` prefix events will be handled by `AppListener::allEvent()`
$em->attach('app.*', [$groupListener, 'allEvent']);

// create app
$app = new App($em);

// run.
$app->run();
