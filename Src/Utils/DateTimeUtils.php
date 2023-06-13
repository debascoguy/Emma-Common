<?php

namespace Emma\Common\Utils;

use DateTime;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */
class DateTimeUtils
{
    /**
     * @param $date
     * @return bool
     */
    public static function isValidDateTime($date) 
    {
        if (empty($date) || "00/00/0000" == $date || !($date instanceof DateTime)) {
            return false;
        }
        return self::isValidDate($date);
    }

    /**
     * @param $date
     * @return bool
     */
    public static function isValidDate($date)
    {
        if (empty($date) || "00/00/0000" == $date) {
            return false;
        }

        if (is_string($date)) {
            $test = strtotime($date);
            return !($test == false && $test == -1);
        }

        if ($date instanceof DateTime) {
            $date = $date->format("Y-m-d");
        }

        if (self::createValidDateTimeMysqlFormat($date) instanceof DateTime) {
            return true;
        }
        
        if (self::createValidDateTimeLocaleFormat($date) instanceof DateTime) {
            return true;
        }
        
        return false;
    }

    public static function createValidDateTimeFormat($date, $format = 'Y-m-d') 
    {
        $dateTime = DateTime::createFromFormat($format, $date);
        $errors = DateTime::getLastErrors();
        if (count($errors["warnings"]) > 0 || count($errors["errors"]) > 0) {
            $dateTime = null;
        }
        return $dateTime;
    }

    public static function createValidDateTimeMysqlFormat($date) 
    {
        return self::createValidDateTimeFormat($date, 'm/d/Y');
    }

    public static function createValidDateTimeLocaleFormat($date) 
    {
        return self::createValidDateTimeFormat($date, 'Y-m-d');
    }

    /**
     * @return DateTime
     */
    public static function getTodaysDate() 
    {
        return new DateTime("today midnight");
    }

    /**
     * @return DateTime
     */
    public static function toDateTime($value, $format = 'Y-m-d H:i:s') 
    {
        $date = self::createValidDateTimeFormat($value, $format);
        return $date ? $date : self::getDefaultDateTime();
    }

    /**
     * @return DateTime
     */
    public static function getCurrentDateTime() 
    {
        return new DateTime();
    }

    /**
     * @return DateTime
     */
    public static function getDefaultDateTime() 
    {
        return new DateTime(self::getDefaultDateTimeString());
    }

    /**
     * @return string
     */
    public static function getDefaultDateTimeString() 
    {
        return "0000-00-00 00:00:00";
    }

    /**
     * @return string
     */
    public static function getCurrentDateTimeString() 
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * @return string
     */
    public static function toString($date, $format = 'm/d/Y', $default = '') 
    {
        if (!($date instanceof DateTime)) {
            return self::getDateTime($date, $format);
        }
        return self::isValidDateTime($date) ? $date->format($format) : $default;
    }

    /**
     * @param string $str_date
     * @return bool|string
     */
    public static function getDateTime($str_date = "", $format = 'Y-m-d H:i:s')
    {
        return self::isValidDate($str_date) ? date($format, strtotime($str_date)) : date($format);
    }

    /**
     * @param \DateTime $date1
     * @param \DateTime $date2
     * @param $type
     * @return float
     */
    public static function getDateDifference(\DateTime $date1, \DateTime $date2, $type)
    {
        if ($date2->getTimestamp() > $date1->getTimestamp()) {
            return self::getDateDifference($date2, $date1, $type);
        }
        switch ($type) {
            case "seconds"  :
                return floor($date1->diff($date2)->s);
            /** Seconds */
            case "minutes"  :
                return floor($date1->diff($date2)->i);
            /** Minutes */
            case "hours"  :
                return floor($date1->diff($date2)->h);
            /** Hours */
            case "weeks"  :
                return floor($date1->diff($date2)->days / 7);
            /** Weeks */
            case "months"  :
                return floor($date1->diff($date2)->m);
            /** Months */
            case "years"  :
                return floor($date1->diff($date2)->y);
            /** Years */
            case "days"  :
            default :
                return floor($date1->diff($date2)->days);
            /** Days */
        }
    }

    /**
     * @param $date
     * @return string
     */
    public static function getDateDiff($date)
    {
        $mydate = date("Y-m-d H:i:s");
        //echo $mydate;//2014-06-06 21:35:55
        $datetime1 = date_create($date);
        $datetime2 = date_create($mydate);
        $interval = date_diff($datetime1, $datetime2);
        //echo $interval->format('%s Seconds %i Minutes %h Hours %d days %m Months %y Year    Ago')."<br>";
        $min = $interval->format('%i');
        $sec = $interval->format('%s');
        $hour = $interval->format('%h');
        $mon = $interval->format('%m');
        $day = $interval->format('%d');
        $year = $interval->format('%y');
        if ($interval->format('%i%h%d%m%y') == "00000") {
            //echo $interval->format('%i%h%d%m%y')."<br>";
            return $sec . " Seconds";

        } else if ($interval->format('%h%d%m%y') == "0000") {
            return $min . " Minutes";
        } else if ($interval->format('%d%m%y') == "000") {
            return $hour . " Hours";
        } else if ($interval->format('%m%y') == "00") {
            return $day . " Days";
        } else if ($interval->format('%y') == "0") {
            return $mon . " Months";
        } else {
            return $year . " Years";
        }

    }


    /**
     * @param \DateTime $date
     * @return \DateTime[]
     */
    public static function getQuarterRangeFromDate(\DateTime $date)
    {
        $quarter = (int)ceil($date->format('m') / 3);

        $year = $date->format('Y');
        $startMonth = $quarter * 3 - 2;
        $startDay = 1;

        $start = new \DateTime();
        $start->setDate($year, $startMonth, $startDay);

        $end = new \DateTime();
        $end->setDate($year, $startMonth + 3, $startDay);
        $end->modify('-1 day');

        return array($start, $end);
    }

    /**
     * @param int $referenceDateUnixTimestamp
     * @param string $format
     * @return array
     */
    public static function getFirstAndLastDatesOfMonth($referenceDateUnixTimestamp = 0, $format = 'm/d/Y')
    {
        if ($referenceDateUnixTimestamp == 0) {
            $referenceDateUnixTimestamp = strtotime("now");
        }
        $date = date("Y-m-01", $referenceDateUnixTimestamp);
        $dates = array(
            date($format, $date),
            date('m/t/Y', $date),
        );

        if (isset($format)) {
            $dates[1] = date($format, strtotime($dates[1]));
        }

        return $dates;
    }

    /**
     * @param int $referenceDateUnixTimestamp
     * @param string $format
     * @return array
     */
    public static function getFirstAndLastDatesForPreviousMonth($referenceDateUnixTimestamp = 0, $format = 'm/d/Y')
    {
        if ($referenceDateUnixTimestamp == 0) {
            $referenceDateUnixTimestamp = strtotime("now");
        }
        $date = strtotime("first day of previous month", $referenceDateUnixTimestamp);
        $dates = array(
            date($format, $date),
            date('m/t/Y', $date),
        );

        if (isset($format)) {
            $dates[1] = date($format, strtotime($dates[1]));
        }

        return $dates;
    }

    /**
     * @param int $referenceDateUnixTimestamp
     * @param string $format
     * @return array
     */
    public static function getFirstAndLastDatesForNextMonth($referenceDateUnixTimestamp = 0, $format = 'm/d/Y')
    {
        if ($referenceDateUnixTimestamp == 0) {
            $referenceDateUnixTimestamp = strtotime("now");
        }
        $date = strtotime("first day of next month", $referenceDateUnixTimestamp);
        $dates = array(
            date($format, $date),
            date('m/t/Y', $date),
        );

        if (isset($format)) {
            $dates[1] = date($format, strtotime($dates[1]));
        }

        return $dates;
    }

    /**
     * @param string $format
     * @param int $referenceDateUnixTimestamp
     * @return string
     */
    public static function getFirstOfPreviousMonth($format = 'm/d/Y', $referenceDateUnixTimestamp = 0)
    {
        if ($referenceDateUnixTimestamp == 0) {
            $referenceDateUnixTimestamp = strtotime("now");
        }
        $date = strtotime("first day of previous month", $referenceDateUnixTimestamp);
        return date($format, $date);
    }

    /**
     * @param string $format
     * @param int $referenceDateUnixTimestamp
     * @return string
     */
    public static function getFirstOfNextMonth($format = 'm/d/Y', $referenceDateUnixTimestamp = 0)
    {
        if ($referenceDateUnixTimestamp == 0) {
            $referenceDateUnixTimestamp = strtotime("now");
        }
        $date = strtotime("first day of next month", $referenceDateUnixTimestamp);
        return date($format, $date);
    }

    /**
     * @param string $format
     * @param int $referenceDateUnixTimestamp
     * @return string
     */
    public static function getEquivalentDateOfNextMonth($format = 'm/d/Y', $referenceDateUnixTimestamp = 0)
    {
        if ($referenceDateUnixTimestamp == 0) {
            $referenceDateUnixTimestamp = strtotime("now");
        }
        $referenceDay = date("d", $referenceDateUnixTimestamp);
        $firstOfNextMonth = strtotime("first day of next month", $referenceDateUnixTimestamp);
        $lastOfNextMonth = date("t", strtotime($firstOfNextMonth));
        if ($referenceDay > $lastOfNextMonth) {
            $referenceDay -= ($referenceDay - $lastOfNextMonth);
        }
        $dateInfo = date_create(strtotime($firstOfNextMonth));
        $month = $dateInfo->format("m");
        $year = $dateInfo->format("Y");

        return date($format, mktime(0, 0, 0, $month, $referenceDay, $year));
    }

    /**
     * @param string $format
     * @param int $priorMonths
     * @param int $referenceDateUnixTimestamp
     * @return string
     */
    public static function getFirstOfEarlierMonth($format = 'm/d/Y', $priorMonths = 1, $referenceDateUnixTimestamp = 0)
    {
        if ($referenceDateUnixTimestamp == 0) {
            $referenceDateUnixTimestamp = strtotime("now");
        }
        return date($format, mktime(0, 0, 0,
            date("m", $referenceDateUnixTimestamp) - $priorMonths,
            1,
            date("Y", $referenceDateUnixTimestamp)));
    }

    /**
     * @param string $format
     * @param int $addMonths
     * @param int $referenceDateUnixTimestamp
     * @return string
     */
    public static function getFirstOfLaterMonth($format = 'm/d/Y', $addMonths = 1, $referenceDateUnixTimestamp = 0)
    {
        if ($referenceDateUnixTimestamp == 0) {
            $referenceDateUnixTimestamp = strtotime("now");
        }
        return date($format, mktime(0, 0, 0,
            date("m", $referenceDateUnixTimestamp) + $addMonths,
            1,
            date("Y", $referenceDateUnixTimestamp)));
    }

    /**
     * @param string $format
     * @param int $priorMonths
     * @param int $referenceDateUnixTimestamp
     * @return string
     */
    public static function getEquivalentDateOfEarlierMonth(
        $format = 'm/d/Y',
        $priorMonths = 1,
        $referenceDateUnixTimestamp = 0
    )
    {
        if ($referenceDateUnixTimestamp == 0) {
            $referenceDateUnixTimestamp = strtotime("now");
        }

        $firstOfEarlierMonth = date($format, mktime(0, 0, 0,
            date("m", $referenceDateUnixTimestamp) - $priorMonths,
            1,
            date("Y", $referenceDateUnixTimestamp)));
        $referenceDay = date("d", $referenceDateUnixTimestamp);
        $lastOfEarlierMonth = date("t", strtotime($firstOfEarlierMonth));
        if ($referenceDay > $lastOfEarlierMonth) {
            $referenceDay -= ($referenceDay - $lastOfEarlierMonth);
        }
        $dateInfo = date_parse($firstOfEarlierMonth);
        $month = $dateInfo['month'];
        $year = $dateInfo['year'];

        return date($format, mktime(0, 0, 0, $month, $referenceDay, $year));
    }
}