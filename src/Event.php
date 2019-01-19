<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 16/8/27
 * Time: 下午12:34
 * @link windwalker https://github.com/ventoviro/windwalker
 */

namespace Inhere\Event;

/**
 * Class Event
 * @package Inhere\Event
 */
class Event implements EventInterface, \Serializable
{
    /**
     * @var string Event name
     */
    protected $name = '';

    /**
     * @var array Event params
     */
    protected $params = [];

    /**
     * @var null|string|mixed Event target
     */
    protected $target;

    /**
     * Stop execution of the listener queue associated with the event
     * @var boolean
     */
    protected $stopped = false;

    public static function create(string $name = '', array $params = []): self
    {
        return new static($name, $params);
    }

    /**
     * @param string $name
     * @param array  $params
     * @throws \InvalidArgumentException
     */
    public function __construct(string $name = '', array $params = [])
    {
        if ($name) {
            $this->setName($name);
        }

        if ($params) {
            $this->params = $params;
        }
    }

    /**
     * @param string $name
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function checkName(string $name)
    {
        $name = \trim($name, '. ');

        if (!$name || \strlen($name) > 64) {
            throw new \InvalidArgumentException('Setup the name can be a not empty string of not more than 64 characters!');
        }

        return $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public function setName($name)
    {
        $this->name = self::checkName($name);
    }

    /**
     * set all params
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function addParams(array $params)
    {
        $this->params = \array_merge($this->params, $params);
        return $this;
    }

    /**
     * get all param
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * clear all param
     */
    public function clearParams()
    {
        $this->params = [];
    }

    /**
     * add a argument
     * @param string|int $name
     * @param mixed      $value
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function addParam($name, $value)
    {
        if (!isset($this->params[$name])) {
            $this->setParam($name, $value);
        }

        return $this;
    }

    /**
     * set a param to the event
     * @param string|int $name
     * @param mixed      $value
     * @throws  \InvalidArgumentException  If the argument name is null.
     */
    public function setParam($name, $value)
    {
        if (null === $name) {
            throw new \InvalidArgumentException('The argument name cannot be null.');
        }

        $this->params[$name] = $value;
    }

    /**
     * @param string|int $name
     * @param null       $default
     * @return null
     */
    public function getParam($name, $default = null)
    {
        return $this->params[$name] ?? $default;
    }

    /**
     * @param string|int $name
     * @return bool
     */
    public function hasParam($name): bool
    {
        return isset($this->params[$name]);
    }

    /**
     * @param string|int $name
     */
    public function removeParam(string $name)
    {
        if (isset($this->params[$name])) {
            unset($this->params[$name]);
        }
    }

    /**
     * Get target/context from which event was triggered
     * @return null|string|mixed
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set the event target
     * @param  null|string|mixed $target
     * @return void
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * Indicate whether or not to stop propagating this event
     * @param  bool $flag
     */
    public function stopPropagation($flag)
    {
        $this->stopped = (bool)$flag;
    }

    /**
     * Has this event indicated event propagation should stop?
     * @return bool
     */
    public function isPropagationStopped(): bool
    {
        return $this->stopped;
    }

    /**
     * @return string
     */
    public function serialize(): string
    {
        return \serialize([$this->name, $this->params, $this->stopped]);
    }

    /**
     * Unserialize the event.
     * @param   string $serialized The serialized event.
     * @return  void
     */
    public function unserialize($serialized)
    {
        // ['allowed_class' => null]
        [$this->name, $this->params, $this->stopped] = \unserialize($serialized, ['allowed_class' => null]);
    }
}
