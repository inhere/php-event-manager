<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/10/16
 * Time: 下午10:35
 */

error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('Asia/Shanghai');

spl_autoload_register(function ($class) {
    if (0 === strpos($class,'Inhere\Event\Examples\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('Inhere\Event\Examples\\')));
        $file =__DIR__ . "/{$path}.php";

        if (is_file($file)) {
            include $file;
        }

    } elseif (0 === strpos($class,'Inhere\Event\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('Inhere\Event\\')));
        $file = dirname(__DIR__) . "/src/{$path}.php";

        if (is_file($file)) {
            include $file;
        }
    }
});


$myEvent = new class extends \Inhere\Event\Event {
    protected $name = 'test';

    public $prop = 'value';
};

$myListener = new class {
    public function __invoke(\Inhere\Event\EventInterface $event)
    {
        echo "handle the event {$event->getName()}\n";
    }

    const ON_DB_UPDATE = 'onDbUpdate';

    public function onDbUpdate(\Inhere\Event\EventInterface $event)
    {
        echo "handle the event {$event->getName()}, sql: {$event->getParam('sql')}\n";
    }
};

$mgr = new \Inhere\Event\EventManager();

//
$mgr->attach('test', $myListener);
$evt = $mgr->trigger('test');


// auto bind method 'onDbUpdate'
$mgr->addListener($myListener);

$evt1 = $mgr->trigger($myListener::ON_DB_UPDATE, null, ['sql' => 'a sql string']);

//var_dump($evt1);