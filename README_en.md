# Event Dispatcher

[![License](https://img.shields.io/packagist/l/inhere/event.svg?style=flat-square)](LICENSE)
[![Php Version](https://img.shields.io/badge/php-%3E=7.1.0-brightgreen.svg?maxAge=2592000)](https://packagist.org/packages/inhere/event)
[![Latest Stable Version](http://img.shields.io/packagist/v/inhere/event.svg)](https://packagist.org/packages/inhere/event)

简洁, 功能完善的事件管理调度实现

- 实现自 [Psr 14](https://github.com/php-fig/fig-standards/blob/master/proposed/event-dispatcher.md) - 事件调度器
- 支持对一个事件添加多个监听器
- 支持设置事件优先级
- 支持快速的事件组注册
- 支持根据事件名称来快速的对事件组监听
  - eg 触发 `app.run`, `app.end` 都将同时会触发 `app.*` 事件
- 支持通配符事件的监听

## 项目地址

- **github** https://github.com/inhere/php-event-manager.git

## 安装

- composer 命令

```php
composer require inhere/event
```

- composer.json

```json
{
    "require": {
        "inhere/event": "dev-master"
    }
}
```

### 事件调度器

事件调度器, 也可称之为事件管理器。事件的注册、监听器注册、调度(触发)都是由它管理的。

```php
use Inhere\Event\EventManager;

$em = new EventManager();
```

## 事件监听器

监听器允许是: 

1. function 函数
2. 一个闭包
3. 一个监听器类(可以有多种方式)

### 1. function

```php
// ... 

$em->attach(Mailer::EVENT_MESSAGE_SENT, 'my_function');
```

### 2. 闭包

```php
// ... 

$em->attach(Mailer::EVENT_MESSAGE_SENT, function(Event $event) {
    // $message = $event->message;
    // ... some logic
});
```

### 3. 监听器类(有多种方式)

- 类里面存在跟事件相同名称的方法

> 此种方式可以在类里面写多个事件的处理方法

```php
class ExamListener1
{
    public function messageSent(EventInterface $event)
    {
        echo "handle the event {$event->getName()}\n";
    }
}
```

- 一个类(含有 `__invoke` 方法)

> 此时这个类对象就相当于一个闭包

```php
class ExamListener2
{
    public function __invoke(EventInterface $event)
    {
        echo "handle the event {$event->getName()}\n";
    }
}
```

- 实现接口 `EventHandlerInterface`

触发时会自动调用 `handle()` 方法。

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
```

- 实现接口 `EventSubscriberInterface`

可以在一个类里面自定义监听多个事件

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
     * 配置事件与对应的处理方法
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            self::TEST_EVENT => 'onTest',
            self::POST_EVENT => ['onPost', ListenerPriority::LOW], // 还可以配置优先级
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

## 快速使用

### 1. 绑定事件触发

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
        // ...发送 $message 的逻辑...

        $event = new MessageEvent(self::EVENT_MESSAGE_SENT);
        $event->message = $message;
        
        // 事件触发
        $this->eventManager->trigger($event);
    }
}
```

### 2. 触发事件

```php
$em = new EventManager();

// 绑定事件
$em->attach(Mailer::EVENT_MESSAGE_SENT, 'exam_handler');
$em->attach(Mailer::EVENT_MESSAGE_SENT, function (EventInterface $event)
{
    $pos = __METHOD__;
    echo "handle the event {$event->getName()} on the: $pos\n";
});

// 这里给它设置了更高的优先级
$em->attach(Mailer::EVENT_MESSAGE_SENT, new ExamListener1(), 10);
$em->attach(Mailer::EVENT_MESSAGE_SENT, new ExamListener2());
$em->attach(Mailer::EVENT_MESSAGE_SENT, new ExamHandler());

$mailer = new Mailer();
$mailer->setEventManager($em);

// 执行，将会触发事件
$mailer->send('hello, world!');
```

### 3. 运行示例

完整的实例代码在 `examples/demo.php` 中。

运行: `php examples/demo.php`

输出：

```text
$ php examples/exam.php
handle the event 'messageSent' on the: ExamListener1::messageSent // 更高优先级的先调用
handle the event 'messageSent' on the: exam_handler
handle the event 'messageSent' on the: {closure}
handle the event 'messageSent' on the: ExamListener2::__invoke
handle the event 'messageSent' on the: Inhere\Event\Examples\ExamHandler::handle

```

## 一组事件的监听器

除了一些特殊的事件外，在一个应用中，大多数事件是有关联的，此时我们就可以对事件进行分组，方便识别和管理使用。

- **事件分组**  推荐将相关的事件，在名称设计上进行分组

例如：

```text
// 模型相关：
model.insert
model.update
model.delete

// DB相关：
db.connect
db.disconnect
db.query

// 应用相关：
app.start
app.run
app.stop
```

### 1. 一个简单的示例应用类

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

### 2. 此应用的监听器类

将每个事件的监听器写一个类，显得有些麻烦。我们可以只写一个类用里面不同的方法来处理不同的事件。

- 方式一： **类里面存在跟事件名称相同的方法**(`app.start` -> `start()`)

> 这种方式简单快捷，但是限定较死 - 事件名与方法的名称必须相同。

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

- 方式二：实现接口 `EventSubscriberInterface`

有时候我们并不想将处理方法定义成事件名称一样，想自定义。

此时我们可以实现接口 `EventSubscriberInterface`，通过里面的 `getSubscribedEvents()` 来自定义事件和对应的处理方法

> 运行示例请看 `examples/enum-group.php`

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
     * 配置事件与对应的处理方法
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            self::TEST_EVENT => 'onTest',
            self::POST_EVENT => ['onPost', ListenerPriority::LOW], // 还可以配置优先级
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

### 3. 添加监听

```php
// 这里使用 方式一
$em = new EventManager();

// register a group listener
$em->attach('app', new AppListener());

// create app
$app = new App($em);

// run.
$app->run();
```

### 4. 运行示例

完整的示例代码在 `examples/named-group.php` 中。

运行: `php examples/named-group.php`

输出：

```text
$ php examples/named-group.php
handle the event 'app.start' on the: Inhere\Event\Examples\AppListener::start
handle the event 'app.beforeRequest' on the: Inhere\Event\Examples\AppListener::beforeRequest
request handling ....
handle the event 'app.afterRequest' on the: Inhere\Event\Examples\AppListener::afterRequest
handle the event 'app.stop' on the: Inhere\Event\Examples\AppListener::stop

```

## 事件通配符 `*`

支持使用事件通配符 `*` 对一组相关的事件进行监听, 分两种。

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

### 运行示例

运行: `php examples/named-group.php`
输出：(_可以看到每个事件都经过了`AppListener::allEvent()`的处理_)

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

## 事件对象

事件对象 - 装载了在触发事件时相关的上下文信息，用户自定义的。

### 预先创建一个事件

- 直接简单的使用类 `Event`

```php
$myEvent = new Event('name', 'target', [ 'some params ...' ]);
```

- 使用继承了 `Event` 的子类

这样你可以追加自定义数据

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

MIT
