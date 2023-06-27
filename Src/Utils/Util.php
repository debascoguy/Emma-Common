<?php

namespace Emma\Stdlib;

/**
 * Class Util
 * @deprecated
 */
class Util
{

    /**
     * Strip all witespaces from the given string.
     *
     * @param  string $string The string to strip
     * @return string
     */
    public static function strip_space($string)
    {
        return trim(preg_replace('/\s+/', '', $string));
    }

    /**
     * @return string
     */
    public static function generateCookie()
    {
        return sha1(self::generateSession());
    }

    /**
     * @return string
     */
    public static function generateSession()
    {
        $sessionId = substr(md5(rand(10000, 99999) * microtime() * time()), 0, 30);
        return $sessionId;
    }

    /**
     * @return mixed
     */
    public static function getIPAddress()
    {
        global $_SERVER, $HTTP_SERVER_VARS;
        return ((int)\phpversion() >= 5) ? $_SERVER["REMOTE_ADDR"] : $HTTP_SERVER_VARS["REMOTE_ADDR"];
    }

    /**
     * @return string
     */
    public static function getComputerName()
    {
        return ((double)\phpversion() >= 5.3) ? \gethostname() : \php_uname('n');
    }

    /**
     * @delete_all_cookies
     */
    public static function delete_all_cookies()
    {
        // unset cookies
        if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach ($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                if (function_exists('setcookie')) {
                    \setcookie($name, '', time() - 1000);
                    \setcookie($name, '', time() - 1000, '/');
                }
            }
        }
    }

    /**
     * @param $name
     */
    public static function deleteCookie($name)
    {
        setcookie($name, '', time() - 1000);
        setcookie($name, '', time() - 1000, '/');
    }

    /**
     * GET the Current Page URL
     * @param bool $show_www
     * @return mixed|string
     */
    public static function getCurrentURL($show_www = true)
    {
        $pageURL = ($_SERVER["HTTPS"] == "on") ? 'https://' : 'http://';
        $pageURL .= ($_SERVER["SERVER_PORT"] != "80") ?
            $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"]
            : $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];

        return ($show_www) ? $pageURL : str_replace("www.", "", $pageURL);
    }

    /**
     * @param string $append
     * @return string
     */
    public static function getHomeURL($append = "")
    {
        $pageURL = ($_SERVER["HTTPS"] == "on") ? 'https://' : 'http://';
        $pageURL .= ($_SERVER["SERVER_PORT"] != "80") ?
            $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] : $_SERVER["SERVER_NAME"];
        return $pageURL . $append;
    }

    /**
     * @param $value
     * @return int
     */
    public static function blank2number($value)
    {
        return (!isset($value) || empty($value)) ? 0 : 1;
    }

    /**
     * @param $value
     * @return int
     */
    public static function blank2Value($value)
    {
        return (!isset($value) || empty($value)) ? 0 : $value;
    }

    /**
     *
     */
    public function redirectToHTTPS()
    {
        $redirect = self::getCurrentURL();
        header("Location:$redirect");
    }

    /**
     * @param $phone
     * @return string
     */
    public function formatPhoneUSA($phone)
    {
        $phone =  preg_replace("/[^0-9]/","",$phone);
        return "(".substr($phone, 0, 3).") ".substr($phone, 3, 3)."-".substr($phone,6);
    }

    /**
     * @param string $html
     * @return string
     */
    public function loadStaticHTML($html = 'index.html')
    {
        if (is_file($html)){
            $file = file_get_contents($html, true);
            return $file;
        }
        return $html;
    }

    /**
     * @param null $str_date
     * @return bool|string
     */
    public function getDateTimeFromUnixTime($str_date = null)
    {
        return (!empty($str_date)) ? date('Y-m-d H:i:s', $str_date) : date('Y-m-d H:i:s');
    }

    /**
     * @param string $str_date
     * @return bool|string
     */
    public function getDateTime($str_date="")
    {
        $phpdate = strtotime($str_date);
        return $this->getDateTimeFromUnixTime($phpdate);
    }


}
