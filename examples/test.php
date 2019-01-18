<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/10/16
 * Time: 下午10:35
 */

include dirname(__DIR__) . '/test/boot.php';

$myEvent = new class extends \Inhere\Event\Event
{
    protected $name = 'test';

    public $prop = 'value';
};

$myListener = new class
{
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
