# Event Dispatcher

[![License](https://img.shields.io/packagist/l/inhere/event.svg?style=flat-square)](LICENSE)
[![Php Version](https://img.shields.io/badge/php-%3E=7.1.0-brightgreen.svg?maxAge=2592000)](https://packagist.org/packages/inhere/event)
[![Latest Stable Version](http://img.shields.io/packagist/v/inhere/event.svg)](https://packagist.org/packages/inhere/event)

> **[中文README](./README.md)**

Simple, fully functional event management scheduling implementation

- Implements the [Psr 14](https://github.com/php-fig/fig-standards/blob/master/proposed/event-dispatcher.md) - event dispatcher
- Support for adding multiple listeners to an event
- Support for setting event priority
- Support for fast event group registration
- Support for quick event group monitoring based on event name
  - eg Trigger `app.run`, `app.end` will also trigger the `app.*` event
- Support for monitoring of wildcard events

## Github

- **github** https://github.com/inhere/php-event-manager.git

## Install

- by composer require

```php
composer require inhere/event
```

- by `composer.json`

```json
{
    "require": {
        "inhere/event": "^1.0"
        // "inhere/event": "dev-master"
    }
}
```

### Event dispatcher
    
The event dispatcher, also known as the event manager. 

Event registration, listener registration, and dispatcher (triggering) are all managed by it.

```php
use Inhere\Event\EventManager;

$em = new EventManager();
```

## Event listener

listener can be: 

1. function name
2. a closure
3. a class(There are many ways)

### 1. Function

```php
// ... 

$em->attach(Mailer::EVENT_MESSAGE_SENT, 'my_function');
```

### 2. Closure

```php
// ... 

$em->attach(Mailer::EVENT_MESSAGE_SENT, function(Event $event) {
    // $message = $event->message;
    // ... some logic
});
```

### 3. Listener class

#### Method with the same name of the event

a method with the same name as the event in the class

> This way you can write multiple event handlers in a class.

```php
class ExamListener1
{
    public function messageSent(EventInterface $event)
    {
        echo "handle the event {$event->getName()}\n";
    }
    
    public function otherEvent(EventInterface $event)
    {
        echo "handle the event {$event->getName()}\n";
    }
}

// register
$em->addListener('group name', new ExamListener1);
```

#### A class (with the `__invoke` method)

> At this point, this class object is equivalent to a closure.

```php
class ExamListener2
{
    public function __invoke(EventInterface $event)
    {
        echo "handle the event {$event->getName()}\n";
    }
}

// register
$em->addListener('event name', new ExamListener2);
```

#### Implements `EventHandlerInterface`

The `handle()` method is called automatically when triggered.

```php
class ExamHandler implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     * @return mixed
     */
    public function handle(EventInterface $event)
    {
        // TODO: Implement handle() method.
    }
}

// register
$em->addListener('event name', new ExamListener2);
```

#### Implements `EventSubscriberInterface`

Can customize multiple events in one class, and allow configure priority. 

```php
/**
 * Class EnumGroupListener
 * @package Inhere\Event\Examples
 */
class EnumGroupListener implements EventSubscriberInterface
{
    const TEST_EVENT = 'test';
    const POST_EVENT = 'post';

    /**
     * Configuration events and corresponding processing methods
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            self::TEST_EVENT => 'onTest',
            // can also configure priority
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
```

## Quick use

### 1. Prepare

```php
// a pre-defined event
class MessageEvent extends Event
{
    // append property ... 
    public $message;
}

// in the business
class Mailer
{
    use EventManagerAwareTrait;

    const EVENT_MESSAGE_SENT = 'messageSent';

    public function send($message)
    {
        // ... do send $message ...

        $event = new MessageEvent(self::EVENT_MESSAGE_SENT);
        $event->message = $message;
        
        // will event trigger
        $this->eventManager->trigger($event);
    }
}
```

### 2. Binding events and trigger

```php
$em = new EventManager();

// binding events
$em->attach(Mailer::EVENT_MESSAGE_SENT, 'exam_handler');
$em->attach(Mailer::EVENT_MESSAGE_SENT, function (EventInterface $event)
{
    $pos = __METHOD__;
    echo "handle the event {$event->getName()} on the: $pos\n";
});

// add more listeners ...
// give it a higher priority here.
$em->attach(Mailer::EVENT_MESSAGE_SENT, new ExamListener1(), 10);
$em->attach(Mailer::EVENT_MESSAGE_SENT, new ExamListener2());
$em->attach(Mailer::EVENT_MESSAGE_SENT, new ExamHandler());

$mailer = new Mailer();
$mailer->setEventManager($em);

// Execution will trigger the event
$mailer->send('hello, world!');
```

### 3. Running example

The complete example code is in `examples/demo.php`.

Running: `php examples/demo.php`
Output：

```text
$ php examples/exam.php
handle the event 'messageSent' on the: ExamListener1::messageSent // Higher priority first call
handle the event 'messageSent' on the: exam_handler
handle the event 'messageSent' on the: {closure}
handle the event 'messageSent' on the: ExamListener2::__invoke
handle the event 'messageSent' on the: Inhere\Event\Examples\ExamHandler::handle

```

## Listening to a set of events

Except for some special events, in an application, most of the events are related, 
so we can group the events for easy identification and management.

- **Event grouping**  It is recommended to group related events in the name design

Example：

```text
// Model related:
model.insert
model.update
model.delete

// DB related:
db.connect
db.disconnect
db.query

// Application related:
app.start
app.run
app.stop
```

### 1. A simple example application class

```php

/**
 * Class App
 * @package Inhere\Event\Examples
 */
class App
{
    use EventManagerAwareTrait;
    
    const ON_START = 'app.start';
    const ON_STOP = 'app.stop';
    const ON_BEFORE_REQUEST = 'app.beforeRequest';
    const ON_AFTER_REQUEST = 'app.afterRequest';
    
    public function __construct(EventManager $em)
    {
        $this->setEventManager($em);

        $this->eventManager->trigger(new Event(self::ON_START, [
            'key' => 'val'
        ]));
    }

    public function run()
    {
        $sleep = 0;
        $this->eventManager->trigger(self::ON_BEFORE_REQUEST);

        echo 'request handling ';
        while ($sleep <= 3) {
            $sleep++;
            echo '.';
            sleep(1);
        }
        echo "\n";

        $this->eventManager->trigger(self::ON_AFTER_REQUEST);
    }

    public function __destruct()
    {
        $this->eventManager->trigger(new Event(self::ON_STOP, [
            'key1' => 'val1'
        ]));
    }
}
```

### 2. Listener class for this app

It would be a bit of a hassle to write a class for each event listener.
We can just write a class to handle different events in different ways.

- Method 1： **There is a method in the class with the same name as the event.**(`app.start` -> `start()`)

> This method is quick and easy, but with certain restrictions - the name of the event and the method must be the same.

```php
/**
 * Class AppListener
 * @package Inhere\Event\Examples
 */
class AppListener
{
    public function start(EventInterface $event)
    {
        $pos = __METHOD__;
        echo "handle the event {$event->getName()} on the: $pos\n";
    }

    public function beforeRequest(EventInterface $event)
    {
        $pos = __METHOD__;
        echo "handle the event {$event->getName()} on the: $pos\n";
    }

    public function afterRequest(EventInterface $event)
    {
        $pos = __METHOD__;
        echo "handle the event {$event->getName()} on the: $pos\n";
    }

    public function stop(EventInterface $event)
    {
        $pos = __METHOD__;
        echo "handle the event {$event->getName()} on the: $pos\n";
    }
}
```

- Method 2：Implementation `EventSubscriberInterface`

Sometimes we don't want to define the processing method as the event name, we want to customize the processing method name.

At this point we can implement the interface `EventSubscriberInterface`, 
through the `getSubscribedEvents()` inside to customize the event and the corresponding processing method

```php
/**
 * Class EnumGroupListener
 * @package Inhere\Event\Examples
 */
class EnumGroupListener implements EventSubscriberInterface
{
    const TEST_EVENT = 'test';
    const POST_EVENT = 'post';

    /**
     * Configuration events and corresponding processing methods
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            self::TEST_EVENT => 'onTest',
            self::POST_EVENT => ['onPost', ListenerPriority::LOW], // setting priority
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
```

> Please see the running example `examples/enum-group.php`

### 3. Register listener

```php
// Note: Use here one way
$em = new EventManager();

// register a group listener
$em->attach('app', new AppListener());

// create app
$app = new App($em);

// run.
$app->run();
```

### 4. Running example

The full sample code is in `examples/named-group.php`.

Running: `php examples/named-group.php`
Output:

```text
$ php examples/named-group.php
handle the event 'app.start' on the: Inhere\Event\Examples\AppListener::start
handle the event 'app.beforeRequest' on the: Inhere\Event\Examples\AppListener::beforeRequest
request handling ....
handle the event 'app.afterRequest' on the: Inhere\Event\Examples\AppListener::afterRequest
handle the event 'app.stop' on the: Inhere\Event\Examples\AppListener::stop

```

## Event wildcard `*`

Support for using the event wildcard `*` to listen for a group of related events, divided into two.

1. `*` 全局的事件通配符。直接对 `*` 添加监听器(`$em->attach('*', 'global_listener')`), 此时所有触发的事件都会被此监听器接收到。
2. `{prefix}.*` 指定分组事件的监听。eg `$em->attach('db.*', 'db_listener')`, 此时所有触发的以 `db.` 为前缀的事件(eg `db.query` `db.connect`)都会被此监听器接收到。

> 当然，你在事件到达监听器前停止了本次事件的传播`$event->stopPropagation(true);`，就不会被后面的监听器接收到了。

示例，在上面的组事件监听器改下，添加一个 `app.*` 的事件监听。

```php
// AppListener 新增一个方法
class AppListener
{
    // ...

    public function allEvent(EventInterface $event)
    {
        $pos = __METHOD__;
        echo "handle the event '{$event->getName()}' on the: $pos\n";
    }
}

// ...

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
```

### Running example

Running: `php examples/named-group.php`
Output: (_You can see that each event has been processed by `AppListener::allEvent()`_)

```text
$ php examples/named-group.php
handle the event 'app.start' on the: Inhere\Event\Examples\AppListener::start
handle the event 'app.start' on the: Inhere\Event\Examples\AppListener::allEvent
handle the event 'app.beforeRequest' on the: Inhere\Event\Examples\AppListener::beforeRequest
handle the event 'app.beforeRequest' on the: Inhere\Event\Examples\AppListener::allEvent
request handling ....
handle the event 'app.afterRequest' on the: Inhere\Event\Examples\AppListener::afterRequest
handle the event 'app.afterRequest' on the: Inhere\Event\Examples\AppListener::allEvent
handle the event 'app.stop' on the: Inhere\Event\Examples\AppListener::stop
handle the event 'app.stop' on the: Inhere\Event\Examples\AppListener::allEvent

```

## Event object

Event Object - Loads the context information associated with the trigger event, user-defined data.

### Create an event in advance

- Direct and simple use of class `Event`

```php
$myEvent = new Event('name', 'target', [ 'some params ...' ]);
```

- Use a subclass that inherits `Event`

> So you can append custom data

```php
// create event class
class MessageEvent extends Event
{
    protected $name = 'messageSent';
    
    // append property ... 
    public $message;
}
```

## License 

[MIT](LICENSE)
