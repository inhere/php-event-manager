# Event manager

implement the psr-14 - Event Manager

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

- 使用基础了 `Event` 的子类

这样你可以追加自定义数据

```php
// 1. create event class
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
class MyListener 
{
    public function messageSent(\Inhere\Event\EventInterface $event)
    {
        echo "handle the event {$event->getName()}\n";
    }
}

// add
$dispatcher->attach(Mailer::EVENT_MESSAGE_SENT, new MyListener);
```

- 一个类(含有 `__invoke` 方法)

```php
class MyListener 
{
    public function __invoke(\Inhere\Event\EventInterface $event)
    {
        echo "handle the event {$event->getName()}\n";
    }
}

// add
$dispatcher->attach(Mailer::EVENT_MESSAGE_SENT, new MyListener);
```

- 一个类(implements the HandlerInterface)

```php
class Listener implements HandlerInterface
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

// add
$dispatcher->attach(Mailer::EVENT_MESSAGE_SENT, new MyListener);
```

### 触发事件

```
// 3. trigger 
class Mailer
{
    const EVENT_MESSAGE_SENT = 'messageSent';

    public function send($message)
    {
        // ...发送 $message 的逻辑...

        $event = new MessageEvent;
        $event->message = $message;
        
        // trigger event
        $dispatcher->trigger(self::EVENT_MESSAGE_SENT, $event);
    }
}
```

## 一组事件的监听器


## License 

MIT