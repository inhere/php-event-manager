<?php


use Inhere\Event\Event;
use Inhere\Event\EventAwareTrait;
use Inhere\Event\EventInterface;
use Inhere\Event\EventManager;
use Inhere\Event\HandlerInterface;

require dirname(__DIR__) . '/tests/boot.php';

function exam1_handler(EventInterface $event)
{
    $pos = __METHOD__;
    echo "handle the event {$event->getName()} on the: $pos \n";
}

class Exam2Listener
{
    public function messageSent(EventInterface $event)
    {
        $pos = __METHOD__;

        echo "handle the event {$event->getName()} on the: $pos \n";
    }
}

class Exam3Listener
{
    public function __invoke(EventInterface $event)
    {
        $pos = __METHOD__;
        echo "handle the event {$event->getName()} on the: $pos\n";
    }
}

class Exam4Handler implements HandlerInterface
{
    /**
     * @param EventInterface $event
     */
    public function handle(EventInterface $event)
    {
        $pos = __METHOD__;
        echo "handle the event {$event->getName()} on the: $pos\n";
    }
}

// 1. create event class
class MessageEvent extends Event
{
    protected $name = 'messageSent';

    // append property ...
    public $message = 'oo a text';
}

class Mailer
{
    use EventAwareTrait;

    const EVENT_MESSAGE_SENT = 'messageSent';

    public function send($message)
    {
        // ...发送 $message 的逻辑...

        $event = new MessageEvent;
        $event->message = $message;

        // trigger event
        $this->eventManager->trigger(self::EVENT_MESSAGE_SENT, $event);

        // var_dump($event);
    }
}

$em = new EventManager();
$em->attach('messageSent', 'exam1_handler');
$em->attach('messageSent', function (EventInterface $event)
{
    $pos = __METHOD__;
    echo "handle the event {$event->getName()} on the: $pos\n";
});
$em->attach('messageSent', new Exam2Listener());
$em->attach('messageSent', new Exam3Listener());
$em->attach('messageSent', new Exam4Handler());

$mailer = new Mailer();
$mailer->setEventManager($em);

$mailer->send('hello, world!');