<?php

namespace Emma\Common\Utils;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 * Date: 4/9/2016
 * Time: 8:52 PM
 */
class StringManagement
{
    
    /**
     * @param int $length
     * @return string
     */
    public static function getUniqueString($length = 32)
    {
        return substr(md5(rand() * microtime() * time()), 0, $length);
    }

    /**
     * Generate a random string, using a cryptographically secure 
     * pseudorandom number generator (random_int)
     *
     * This function uses type hints now (PHP 7+ only), but it was originally
     * written for PHP 5 as well.
     * 
     * For PHP 7, random_int is a PHP core function
     * For PHP 5.x, depends on https://github.com/paragonie/random_compat
     * 
     * @param int $length      How many characters do we want?
     * @param string $keyspace A string of all possible characters
     *                         to select from
     * @return string
     */
    public static function generateRandomString(
        int $length = 64,
        string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    ): string {
        if ($length < 1) {
            throw new \RangeException("Length must be a positive integer");
        }
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            if (function_exists("random_int")){
                $pieces []= $keyspace[random_int(0, $max)];
            }
            else{
                //php5.x that does not support random_int... unsecure.
                $pieces []= $keyspace[rand(0, $max)];
            }
        }
        return implode('', $pieces);
    }

    /**
     * @param $string
     * @param string $replace_with
     * @return string
     */
    public static function strip_space($string, $replace_with = "")
    {
        return trim(preg_replace('/\s+/', $replace_with, $string));
    }

    /**
     * Truncate a string to a specified length without cutting a word off.
     *
     * @param   string $string The string to truncate
     * @param   integer $length The length to truncate the string to
     * @param   string $append Text to append to the string IF it gets
     *                           truncated, defaults to '...'
     * @return  string
     */
    public static function safe_truncate($string, $length, $append = '...')
    {
        $ret = substr($string, 0, $length);
        $last_space = strrpos($ret, ' ');

        if ($last_space !== false && $string != $ret) {
            $ret = substr($ret, 0, $last_space);
        }

        if ($ret != $string) {
            $ret .= $append;
        }

        return $ret;
    }

    /**
     * Truncate the string to given length of charactes.
     *
     * @param $string
     * @param $limit
     * @param string $append
     * @return string
     */
    public static function limit_characters($string, $limit = 100, $append = '...')
    {
        if (mb_strlen($string) <= $limit) {
            return $string;
        }

        return rtrim(mb_substr($string, 0, $limit, 'UTF-8')) . $append;
    }

    /**
     * Truncate the string to given length of words.
     *
     * @param $string
     * @param $limit
     * @param string $append
     * @return string
     */
    public static function limit_words($string, $limit = 100, $append = '...')
    {
        preg_match('/^\s*+(?:\S++\s*+){1,' . $limit . '}/u', $string, $matches);

        if (!isset($matches[0]) || strlen($string) === strlen($matches[0])) {
            return $string;
        }

        return rtrim($matches[0]) . $append;
    }

    /**
     * Returns the ordinal version of a number (appends th, st, nd, rd).
     *
     * @param  string $number The number to append an ordinal suffix to
     * @return string
     */
    public static function ordinal($number)
    {
        $test_c = abs($number) % 10;
        $ext = "";
        if (abs($number) % 100 < 21 && abs($number) % 100 > 4) {
            $ext = 'th';
        }
        else {
            if ($test_c < 4) {
                if ($test_c < 3) {
                    if ($test_c < 2) {
                        if ($test_c < 1) {
                            $ext = 'th';
                        }
                        else{
                            $ext = 'st';
                        }
                    }
                    else{
                        $ext = 'nd';
                    }
                }
                else{
                    $ext = 'rd';
                }
            }
            else{
                $ext = 'th';
            }
        }
        return $number . $ext;
    }


    /**
     * @param $haystack
     * @param $needle
     * @param bool $case_sensitive
     * @return bool
     */
    public static function startsWith($haystack, $needle, bool $case_sensitive = false): bool
    {
        $length = strlen($needle);
        if ($case_sensitive) {
            $value = substr($haystack, 0, $length);
            return (trim($needle) === "") || (strcasecmp($value, $needle) == 0);
        }
        return (trim($needle) === "") || (substr($haystack, 0, $length) === $needle);
    }

    /**
     * @param $haystack
     * @param $needle
     * @param bool $case_sensitive
     * @return bool
     */
    public static function endsWith($haystack, $needle, $case_sensitive = false)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        if ($case_sensitive) {
            $value = substr($haystack, -$length);
            return (trim($needle) === "") || (strcasecmp($value, $needle) == 0);
        }
        return (trim($needle) === "") || (substr($haystack, -$length) === $needle);
    }


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
     * @param $_haystack
     * @param $_needle
     * @param bool $case_sensitive
     * @return bool
     */
    public static function contains($_haystack, $_needle, $case_sensitive = false)
    {
        if (is_array($_haystack)) {
            if (is_array($_needle)) {
                foreach ($_haystack as $elem) {
                    if ($elem === $_needle) {
                        return true;
                    }
                }
                return false;
            }
            return ($case_sensitive) ? in_array($_needle, $_haystack) : self::in_arrayi($_needle, $_haystack);
        } else {
            return ($case_sensitive) ? (strpos($_haystack, $_needle) !== false) : (stripos($_haystack, $_needle) !== false);
        }
    }

    /**
     * @param string $search
     * @param string $replace
     * @param string $subject
     * @return string
     */
    public static function str_ireplace($search, $replace, $subject) {
        $search = preg_quote($search, "/");
        return preg_replace("/".$search."/i", $replace, $subject);
    }

    /**
     * @param string $string
     * @param string $separator
     * @return string
     */
    public static function camelCaseToUnderscore($string, $separator = '_')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1' . $separator . '$2', $string));
    }

    /**
     * @param string $string
     * @param bool $ucFirst
     * @param string $separator
     * @return mixed
     */
    public static function underscoreToCamelCase($string, $ucFirst = false, $separator = '_')
    {
        $str = str_replace(' ', '', ucwords(str_replace($separator, ' ', $string)));
        if (!$ucFirst) {
            $str[0] = strtolower($str[0]);
        }
        return $str;
    }

    /**
     * @param $value
     * @param null $default
     * @return null
     */
    public static function getOrDefault($value, $default = null)
    {
        return (empty($value)) ? $default : $value;
    }

    /**
     * @param string $stringValue
     * @return bool
     */
    public static function toBoolean(string $stringValue): bool
    {
        return filter_var($stringValue, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param string $stringValue
     * @return int
     */
    public static function toInteger(string $stringValue): int
    {
        return filter_var($stringValue, FILTER_VALIDATE_INT);
    }

}