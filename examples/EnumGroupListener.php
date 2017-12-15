<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2017/12/14 0014
 * Time: 22:25
 */

namespace Inhere\Event\Examples;


use Inhere\Event\EventInterface;
use Inhere\Event\EventSubscriberInterface;
use Inhere\Event\ListenerPriority;

/**
 * Class EnumGroupListener
 * @package Inhere\Event\Examples
 */
class EnumGroupListener implements EventSubscriberInterface
{
    const TEST_EVENT = 'test';
    const POST_EVENT = 'post';

    /**
     * 配置事件与对应的处理方法
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            self::TEST_EVENT => 'onTest',
            self::POST_EVENT => ['onPost', ListenerPriority::LOW],
        ];
    }

    public function onTest(EventInterface $event)
    {
        $pos = __METHOD__;
        echo "handle the event {$event->getName()} on the: $pos\n";
    }

    public function onPost(EventInterface $event)
    {
        $pos = __METHOD__;
        echo "handle the event {$event->getName()} on the: $pos\n";
    }
}
