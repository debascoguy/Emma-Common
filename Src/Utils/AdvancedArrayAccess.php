<?php

namespace Emma\Common\Utils;

/**
 * @Author: Ademola Aina
 * User: ademola.aina
 * Date: 11/10/2016
 * Time: 8:24 PM
 */
class AdvancedArrayAccess implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return (isset($this->{$offset}));
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetGet($offset): mixed
    {
        return $this->offsetExists($offset) ? $this->{$offset} : false;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->{$offset} = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        if ($this->offsetExists($offset))
            unset($this->{$offset});
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->getIterator()->count();
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->toAssociativeArray());
    }

    /**
     * @return array (key/values) of all declared public, protected variables of this object or its children
     * Gets the properties of the given object
     * Alternative to :
     */
    public function toAssociativeArray()
    {
        return get_object_vars($this);
    }

    /**
     * @param array $mergeArray
     * @return string : Gets the properties of the given object and Convert it into a Json_encode String.
     * Option: You can merge more values(i.e. An array) to the existing array retrieved from toAssociativeArray() function.
     */
    public function toJsonEncodeString($mergeArray = array())
    {
        return json_encode(array_merge($this->toAssociativeArray(), $mergeArray));
    }

    /**
     * @return array of names of all declared public, protected variables of this object and its children
     */
    public function getClassVars()
    {
        return array_keys($this->toAssociativeArray());
    }

    /**
     * @return array of current values of  all declared public, protected variables of this object or its children
     */
    public function getClassValues()
    {
        return array_values($this->toAssociativeArray());
    }

    /**
     * @param $row
     * @return $this
     */
    public function hydrateInstance($row)
    {
        foreach ($row as $key => $value) {
            $this->offsetSet($key, $value);
        }
        return $this;
    }

}