# Event manager

implement the psr-14 - Event Manager


## usage

### create a dispatcher(the Event Manager)

```php
$dispathcer = new EventManager();
```

### create a event instance.

- use Event

```php
$myEvent = new Event('name', [ 'some params ...' ]);
```

- or use subclass of the Event

```php
// 1. create event class
class MessageEvent extends Event
{
    protected $name = 'messageSent';
    
    // append property ... 
    public $message;
}

```

### add listener

- use closure

```php
// ... 

// 2. add listener (the handler of the event) and relation to the event.

$dispatcher->addListener(function(Event $event) {
    // $message = $event->message;
    // ... some logic
}, Mailer::EVENT_MESSAGE_SENT);
```

- use class(use `__invoke` method)

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

- use class(implements the ListenerInterface)

```php
class Listener implements ListenerInterface
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

### trigger event

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
