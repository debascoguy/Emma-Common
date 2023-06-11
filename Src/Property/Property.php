<?php

namespace Emma\Common\Property;

use Emma\Common\Property\Interfaces\PropertyInterface;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */
class Property implements PropertyInterface
{

    /**
     * @var array
     */
    protected array $parameters = [];

    /**
     * @param array $parameters
     */
    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->parameters)) {
            return $this->parameters[$name];
        }
        return null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->parameters[$name]);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __unset($name)
    {
        unset($this->parameters[$name]);
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     * @return $this
     */
    public function setParameters(array $parameters): static
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * @param mixed $name
     * @param mixed $concreteOrValue
     * @return PropertyInterface|void
     */
    public function register($name, $concreteOrValue)
    {
        $this->parameters[$name] = $concreteOrValue;
    }

    /**
     * @param string $name
     * @param $value
     * @param bool $caseSensitive
     * @return bool
     */
    public function isEqual(string $name, $value, bool $caseSensitive = true): bool
    {
        return $caseSensitive ? $this->get($name) == $value : strtolower($this->get($name)) == strtolower($value);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name): bool
    {
        return array_key_exists($name, $this->parameters);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasValue($name): bool
    {
        return isset($this->parameters[$name]) && !empty($this->parameters[$name]);
    }

    /**
     * @param string $name
     * @param null $default
     * @return null|mixed
     */
    public function get($name, $default = null)
    {
        return isset($this->parameters[$name]) && !is_null($this->parameters[$name]) ? $this->parameters[$name] : $default;
    }

    /**
     * @param $name
     * @return bool
     */
    public function remove($name)
    {
        if ($this->has($name)){
            unset($this->parameters[$name]);
            return true;
        }
        return false;
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->parameters = [];
        return $this;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->parameters);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->parameters);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return  empty($this->parameters);
    }
}