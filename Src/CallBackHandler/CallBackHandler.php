<?php

namespace Emma\Common\CallBackHandler;

use Emma\CallBackHandler\Interfaces\CallBackHandlerInterface;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 * Date: 9/19/2017
 * Time: 8:22 PM
 */
class CallBackHandler implements CallBackHandlerInterface
{
    /**
     * @var string|array|callable
     */
    protected $callback;

    /**
     * @var array
     */
    protected $metadata = [];

    /**
     * @param  string|array|object|callable $callback PHP callback
     * @param  array $metadata Callback metadata
     * @param $isCallableVerified
     */
    public function __construct($callback, array $metadata = [], $isCallableVerified = false)
    {
        $this->metadata = $metadata;
        $this->registerCallback($callback, $isCallableVerified);
    }

    /**
     * @param  string|array|object|callable $callback PHP callback
     * @param  array $metadata Callback metadata
     * @param $isCallableVerified
     * @return self
     */
    public static function create($callback, array $metadata = [], bool $isCallableVerified = false)
    {
        return new self($callback, $metadata, $isCallableVerified);
    }

    /**
     * @param $callBack
     * @param array $args
     * @param bool|false $isCallableVerified
     * @return mixed
     */
    public static function get($callBack, array $args = [], bool $isCallableVerified = false)
    {
        return self::create($callBack, $args, $isCallableVerified)->call();
    }

    /**
     * @param $callBack
     * @param array $args
     * @param bool|false $isCallableVerified
     * @return mixed
     */
    public function __invoke($callBack, $args = [], $isCallableVerified = false)
    {
        return self::get($callBack, $args, $isCallableVerified);
    }

    /**
     * @param $callback
     * @param $isCallableVerified
     * @throws \Exception
     */
    private function registerCallback($callback, $isCallableVerified)
    {
        if ($isCallableVerified) {
            $this->callback = $callback;
        } elseif (is_callable($callback)) {
            $this->callback = $callback;
        } /** TODO This can be removed if you're in 5.3 and above. */
        elseif (is_callable(array($callback, '__invoke'))) {
            $this->callback = $callback;
        } else {
            throw new \BadMethodCallException('Invalid callback provided; not callable');
        }
    }

    /**
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param  array $args Arguments to pass to callback
     * @return mixed
     */
    public function call(array $args = array())
    {
        $callback = $this->getCallback();
        $args = empty($args) ? $this->getMetadata() : array_merge($this->getMetadata(), $args);

        $argCount = count($args);

        /** Performance tweak; use call_user_func() until > 3 arguments reached */
        switch ($argCount) {
            case 0:
                return call_user_func($callback);
            case 1:
                return call_user_func($callback, array_shift($args));
            case 2:
                $arg1 = array_shift($args);
                $arg2 = array_shift($args);
                return call_user_func($callback, $arg1, $arg2);
            case 3:
                $arg1 = array_shift($args);
                $arg2 = array_shift($args);
                $arg3 = array_shift($args);
                return call_user_func($callback, $arg1, $arg2, $arg3);
            default:
                return call_user_func_array($callback, $args);
        }
    }

    /**
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @param  string $name
     * @return mixed
     */
    public function getMetadatum($name)
    {
        if (array_key_exists($name, $this->metadata)) {
            return $this->metadata[$name];
        }
        return null;
    }
}