<?php

namespace Emma\Common\Utils;
use Emma\Common\CallBackHandler\CallBackHandler;

/**
 * Class Array
 */
class ArrayManagement
{
    /**
     * @param $needle
     * @param $haystack
     * @return bool
     */
    public static function in_arrayi($needle, $haystack)
    {
        if (count($haystack) <= 0){
            return false;
        }
        return in_array(strtolower($needle), array_map('strtolower', $haystack));
    }
    
    /**
     * @param $first
     * @param $second
     * @param bool|true $override
     * @return array
     */
    public static function array_merge_custom($first, $second, $override = true)
    {
        $result = array();
        foreach ($first as $key => $value) {
            $result[$key] = $value;
        }
        foreach ($second as $key => $value) {
            if ($override) {
                $result[$key] = $value;
            } else if (empty($result[$key])) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * @param $iterator
     * @param bool|true $use_keys
     * @return array
     */
    public static function iterator_to_array($iterator, $use_keys = true)
    {
        return iterator_to_array($iterator, $use_keys);
    }

    /**
     * Converts objects with public properties to array.
     * Should be used mainly for stdClass conversion.
     *
     * @param object $object
     * @return array
     */
    public static function object_to_array($object)
    {
        return json_decode(json_encode($object), true);
    }

    /**
     * Replaces elements from passed arrays into the first array recursively. Numeric keys will not be replaced.
     * @param array ...$args
     * @return mixed
     */
    public static function join()
    {
        $arrays = func_get_args();

        $original = array_shift($arrays);

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (is_string($key)) {
                    if (is_array($value) && array_key_exists($key, $original) && is_array($original[$key])) {
                        $original[$key] = self::join($original[$key], $value);
                    } else {
                        $original[$key] = $value;
                    }
                } else {
                    $original[] = $value;
                }
            }
        }

        return $original;
    }

    /**
     * Returns the first element in an array.
     * @param  array $array
     * @return mixed
     */
    public static function array_first(array $array)
    {
        return reset($array);
    }


    /**
     * Returns the last element in an array.
     *
     * @param  array $array
     * @return mixed
     */
    public static function array_last(array $array)
    {
        return end($array);
    }


    /**
     * Returns the first key in an array.
     *
     * @param  array $array
     * @return int|string
     */
    public static function array_first_key(array $array)
    {
        reset($array);

        return key($array);
    }

    /**
     * Returns the last key in an array.
     *
     * @param  array $array
     * @return int|string
     */
    public static function array_last_key(array $array)
    {
        end($array);

        return key($array);
    }


    /**
     * Flatten a multi-dimensional array into a one dimensional array.
     *
     * Contributed by Theodore R. Smith of PHP Experts, Inc. <http://www.phpexperts.pro/>
     *
     * @param  array $array The array to flatten
     * @param  boolean $preserve_keys Whether or not to preserve array keys.
     *                                Keys from deeply nested arrays will
     *                                overwrite keys from shallowy nested arrays
     * @return array
     */
    public static function array_flatten(array $array, $preserve_keys = true)
    {
        $flattened = array();

        array_walk_recursive($array, function ($value, $key) use (&$flattened, $preserve_keys) {
            if ($preserve_keys && !is_int($key)) {
                $flattened[$key] = $value;
            } else {
                $flattened[] = $value;
            }
        });

        return $flattened;
    }

    public static function array_flatten_tostring(array $array, $preserve_keys = true)
    {
        $flattened = array();

        array_walk_recursive($array, function ($value, $key) use (&$flattened, $preserve_keys) {
            if ($preserve_keys && !is_int($key)) {
                $flattened[$key] = $value;
            } else {
                $flattened[] = $value;
            }
        });

        return $flattened;
    }


    /**
     * Accepts an array, and returns an array of values from that array as
     * specified by $field. For example, if the array is full of objects
     * and you call util::array_pluck($array, 'name'), the function will
     * return an array of values from $array[]->name.
     *
     * @param  array $array An array
     * @param  string $field The field to get values from
     * @param  boolean $preserve_keys Whether or not to preserve the
     *                                   array keys
     * @param  boolean $remove_nomatches If the field doesn't appear to be set,
     *                                   remove it from the array
     * @return array
     */
    public static function array_pluck(array $array, $field, $preserve_keys = true, $remove_nomatches = true)
    {
        $new_list = array();

        foreach ($array as $key => $value) {
            if (is_object($value)) {
                if (isset($value->{$field})) {
                    if ($preserve_keys) {
                        $new_list[$key] = $value->{$field};
                    } else {
                        $new_list[] = $value->{$field};
                    }
                } elseif (!$remove_nomatches) {
                    $new_list[$key] = $value;
                }
            } else {
                if (isset($value[$field])) {
                    if ($preserve_keys) {
                        $new_list[$key] = $value[$field];
                    } else {
                        $new_list[] = $value[$field];
                    }
                } elseif (!$remove_nomatches) {
                    $new_list[$key] = $value;
                }
            }
        }

        return $new_list;
    }


    /**
     * Searches for a given value in an array of arrays, objects and scalar
     * values. You can optionally specify a field of the nested arrays and
     * objects to search in.
     *
     * @param  array $array The array to search
     * @param  scalar $search The value to search for
     * @param  string $field The field to search in, if not specified
     *                         all fields will be searched
     * @return boolean|scalar  False on failure or the array key on success
     */
    public static function array_search_deep(array $array, $search, $field = false)
    {
        // *grumbles* stupid PHP type system
        $search = (string)$search;

        foreach ($array as $key => $elem) {
            // *grumbles* stupid PHP type system
            $key = (string)$key;

            if ($field) {
                if (is_object($elem) && $elem->{$field} === $search) {
                    return $key;
                } elseif (is_array($elem) && $elem[$field] === $search) {
                    return $key;
                } elseif (is_scalar($elem) && $elem === $search) {
                    return $key;
                }
            } else {
                if (is_object($elem)) {
                    $elem = (array)$elem;

                    if (in_array($search, $elem)) {
                        return $key;
                    }
                } elseif (is_array($elem) && in_array($search, $elem)) {
                    return $key;
                } elseif (is_scalar($elem) && $elem === $search) {
                    return $key;
                }
            }
        }

        return false;
    }

    /**
     * Returns an array containing all the elements of arr1 after applying
     * the callback function to each one.
     *
     * @param  string $callback Callback function to run for each
     *                               element in each array
     * @param  array $array An array to run through the callback
     *                               function
     * @param  boolean $on_nonscalar Whether or not to call the callback
     *                               function on nonscalar values
     *                               (Objects, resources, etc)
     * @return array
     */
    public static function array_map_deep(array $array, $callback, $on_nonscalar = false)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $args = array($value, $callback, $on_nonscalar);
                $array[$key] = CallBackHandler::get(array(__CLASS__, __FUNCTION__), $args);
            } elseif (is_scalar($value) || $on_nonscalar) {
                $array[$key] = CallBackHandler::get($callback, $value);
            }
        }

        return $array;
    }


    /**
     * @param array $array
     * @return array
     */
    public static function array_clean(array $array)
    {
        return array_filter($array);
    }


    /**
     * Access an array index, retrieving the value stored there if it
     * exists or a default if it does not. This function allows you to
     * concisely access an index which may or may not exist without
     * raising a warning.
     *
     * @param  array $var Array value to access
     * @param  mixed $default Default value to return if the key is not
     *                         present in the array
     * @return mixed
     */
    public static function array_get(&$var, $default = null)
    {
        if (isset($var)) {
            return $var;
        }

        return $default;
    }

    /**
     * @param $array
     * @param $position
     * @param $insert
     */
    public function array_insert(&$array, $position, $insert)
    {
        if (is_int($position)) {
            array_splice($array, $position, 0, $insert);
        } else {
            $pos = array_search($position, array_keys($array));
            $array = array_merge(
                array_slice($array, 0, $pos),
                $insert,
                array_slice($array, $pos)
            );
        }
    }

}