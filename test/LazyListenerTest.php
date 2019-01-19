<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2019-01-19
 * Time: 22:50
 */

namespace Inhere\EventTest;

use Inhere\Event\Event;
use Inhere\Event\EventInterface;
use Inhere\Event\Listener\LazyListener;
use PHPUnit\Framework\TestCase;

/**
 * Class LazyListenerTest
 * @package Inhere\EventTest
 */
class LazyListenerTest extends TestCase
{
    public function testCall()
    {
        $listener = LazyListener::create(function (EventInterface $e) {
            $this->assertSame('lazy', $e->getName());
            return 'ABC';
        });

        $this->assertNotEmpty($listener->getCallback());
        $this->assertSame('ABC', $listener->handle(Event::create('lazy')));
    }
}
