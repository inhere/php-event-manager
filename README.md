# Event manager

implement the [Psr 14](https://github.com/php-fig/fig-standards/blob/master/proposed/event-manager.md) - Event Manager

## 项目地址

- **github** https://github.com/inhere/php-event-manager.git
- **git@osc** https://git.oschina.net/inhere/php-event-manager.git

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

- 直接拉取

```bash
git clone https://github.com/inhere/php-event-manager.git
```


## 使用

### 创建事件管理器

创建事件管理器, 也可称之为事件调度器。

```php
$em = new EventManager();
```

### 创建一个事件

- 直接简单的使用类 `Event`

```php
$myEvent = new Event('name', [ 'some params ...' ]);
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

### 创建监听器

监听器允许是: 

- function 函数
- 闭包

```php
// ... 

// 2. add listener (the handler of the event) and relation to the event.

$em->addListener(function(Event $event) {
    // $message = $event->message;
    // ... some logic
}, Mailer::EVENT_MESSAGE_SENT);
```

- 一个类。里面存在跟事件相同名称的方法

```php
class ExamListener1
{
    public function messageSent(\Inhere\Event\EventInterface $event)
    {
        echo "handle the event {$event->getName()}\n";
    }
}
```

- 一个类(含有 `__invoke` 方法)

```php
class ExamListener2
{
    public function __invoke(\Inhere\Event\EventInterface $event)
    {
        echo "handle the event {$event->getName()}\n";
    }
}
```

- 一个类(implements the HandlerInterface)

```php
class ExamHandler implements HandlerInterface
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

### 绑定事件触发

```php
// use trigger 
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
    }
}
```

### 触发事件

```php
$em = new EventManager();

// 绑定事件
$em->attach(Mailer::EVENT_MESSAGE_SENT, 'exam_handler');
$em->attach(Mailer::EVENT_MESSAGE_SENT, function (EventInterface $event)
{
    $pos = __METHOD__;
    echo "handle the event {$event->getName()} on the: $pos\n";
});
$em->attach(Mailer::EVENT_MESSAGE_SENT, new ExamListener1());
$em->attach(Mailer::EVENT_MESSAGE_SENT, new ExamListener2());
$em->attach(Mailer::EVENT_MESSAGE_SENT, new ExamHandler());

$mailer = new Mailer();
$mailer->setEventManager($em);

// 执行，触发事件
$mailer->send('hello, world!');
```

### 运行

完整的实例代码在 `examples/exam.php` 中。

运行: `php examples/exam.php`

输出：

```text
$ php examples/exam.php
handle the event messageSent on the: exam_handler
handle the event messageSent on the: {closure}
handle the event messageSent on the: ExamListener1::messageSent
handle the event messageSent on the: ExamListener2::__invoke
handle the event messageSent on the: Inhere\Event\Examples\ExamHandler::handle

```

## 一组事件的监听器

### 一个简单的应用类

```php

/**
 * Class App
 * @package Inhere\Event\Examples
 */
class App
{
    const ON_START = 'app.start';
    const ON_STOP = 'app.stop';
    const ON_BEFORE_REQUEST = 'app.beforeRequest';
    const ON_AFTER_REQUEST = 'app.afterRequest';

    use EventAwareTrait;

    public function __construct(EventManager $em)
    {
        $this->setEventManager($em);

        $this->eventManager->trigger(self::ON_START, new Event('start', [
            'key' => 'val'
        ]));
    }

    public function run()
    {
        $this->eventManager->trigger(self::ON_BEFORE_REQUEST, new Event('beforeRequest'));

        $sleep = 0;

        echo 'handling ';

        while ($sleep <= 5) {
            $sleep++;

            echo '.';

            sleep(1);
        }

        echo "\n";

        $this->eventManager->trigger(self::ON_AFTER_REQUEST, new Event('afterRequest'));
    }

    public function __destruct()
    {
        $this->eventManager->trigger(self::ON_STOP, new Event('stop', [
            'key1' => 'val1'
        ]));
    }
}
```

### 监听器类

里面存在跟事件相同名称的方法

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

### 准备运行

```php
$em = new EventManager();

// register a group listener
$em->attach('app', new AppListener());

// create app
$app = new App($em);

// run.
$app->run();
```

### 执行

完整的实例代码在 `examples/named-group.php` 中。

运行: `php examples/named-group.php`

输出：

```text
$ php examples/named-group.php
handle the event app.start on the: Inhere\Event\Examples\AppListener::start
handle the event app.beforeRequest on the: Inhere\Event\Examples\AppListener::beforeRequest
handling ......
handle the event app.afterRequest on the: Inhere\Event\Examples\AppListener::afterRequest
handle the event app.stop on the: Inhere\Event\Examples\AppListener::stop

```

## License 

MIT
