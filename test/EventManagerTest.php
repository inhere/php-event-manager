<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-11-27
 * Time: 9:47
 */

namespace Inhere\EventTest;

use Inhere\Event\Event;
use Inhere\Event\EventInterface;
use Inhere\Event\EventManager;
use Inhere\Event\EventManagerInterface;
use Inhere\Event\Examples\ExamHandler;
use PHPUnit\Framework\TestCase;

/**
 * Class EventManagerTest
 */
class EventManagerTest extends TestCase
{
    public function testCreate()
    {
        $em = new EventManager();
        $this->assertInstanceOf(EventManagerInterface::class, $em);
        $this->assertEmpty($em->getParent());
        $this->assertNotEmpty($em->getBasicEvent());
        $this->assertSame(0, $em->countEvents());

        $em->setBasicEvent(new Event('new'));
        $this->assertNotEmpty($evt = $em->getBasicEvent());
        $this->assertSame('new', $evt->getName());

        $em1 = new EventManager();
        $em1->setParent($em);

        $this->assertNotEmpty($em1->getParent());
    }

    public function testAttach()
    {
        $em = new EventManager();
        $l1 = new ExamHandler();
        $em->attach('test', $l1);
        $em->attach('test', function () {
            //
        });

        $this->assertCount(2, $em->getListeners('test'));

        $em->detach('test', $l1);
        $this->assertCount(1, $em->getListeners('test'));
    }

    public function testPriority()
    {
        $l0 = new ExamHandler();
        $l1 = function () {
            //
        };

        $em = new EventManager();
        $em->attach('test', $l0);
        $em->attach('test', $l1, 5);

        $this->assertEquals(0, $em->getListenerPriority($l0, 'test'));
        $this->assertEquals(5, $em->getListenerPriority($l1, 'test'));
    }

    public function testTrigger()
    {
        $l0 = new class
        {
            public function __invoke(Event $evt)
            {
                $evt->addParam('key1', 'val1');
                $evt->setParam('key', 'new val');
            }
        };
        $l1 = function (EventInterface $evt) {
            $evt->setTarget('new target');
        };

        $em = new EventManager();
        $em->attach('test', $l0);
        $em->attach('test', $l1, 5);

        $evt = $em->trigger('test', 'target', ['key' => 'val']);

        $this->assertInstanceOf(EventInterface::class, $evt);
        $this->assertEquals('new target', $evt->getTarget());
        $this->assertEquals('new val', $evt->getParam('key'));
        $this->assertArrayHasKey('key1', $evt->getParams());
    }
}
