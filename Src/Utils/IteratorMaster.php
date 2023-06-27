<?php

namespace Emma\Common\Utils;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 * Date: 12/21/2017
 * Time: 8:43 AM
 */
class IteratorMaster
{

    /**
     * @param array $array
     * @param int $flags
     * @return \ArrayIterator
     * This iterator allows to unset and modify values and keys while iterating
     * over Arrays and Objects.
     * @link http://php.net/manual/en/arrayiterator.construct.php
     */
    public static function getArrayIterator(array $array = array(), $flags = 0)
    {
        return new \ArrayIterator($array, $flags);
    }

    /**
     * @param array|object $input
     * @param int $flags
     * @param string $iterator_class
     * @return \ArrayObject
     * This class allows objects to work as arrays.
     * @link http://php.net/manual/en/class.arrayobject.php
     */
    public static function getArrayObject($input = null, $flags = 0, $iterator_class = "ArrayIterator")
    {
        return new \ArrayObject($input, $flags, $iterator_class);
    }

    /**
     * @return \AppendIterator
     * An Iterator that iterates over several iterators one after the other.
     * @link http://php.net/manual/en/class.appenditerator.php
     */
    public static function getAppendIterator()
    {
        return new \AppendIterator();
    }

    /**
     * @param \Iterator $iterator
     * @param $flags
     * @return \CachingIterator
     * This object supports cached iteration over another iterator.
     * @link http://php.net/manual/en/class.cachingiterator.php
     */
    public static function getCachingIterator(\Iterator $iterator, $flags = \CachingIterator::CALL_TOSTRING)
    {
        return new \CachingIterator($iterator, $flags);
    }

}