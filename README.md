# Event manager

implement the psr-14 - Event Manager

create a dispatcher

```
$dispathcer = new Dispatcher();
```

## usage

create a event instance.

- use Event

```
$event = new Event('name', [ 'some argvments ...' ]);
```

- use subclass of the Event

```
// 1. create event class
class MessageEvent extends Event
{
    protected $name = 'messageSent';
    
    // append property ... 
    public $message;
}

```

- add listener

```
... use

// 2. add listener (the handler of the event) and relation to the event.

$dispatcher->addListener(function(Event $event) {
    // $message = $event->message;
    // ... some logic
}, Mailer::EVENT_MESSAGE_SENT);
```

- trigger event

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
