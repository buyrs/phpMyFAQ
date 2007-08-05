<?php
/**
 * $Id: Utils.php,v 1.7 2007-08-05 16:06:49 thorstenr Exp $
 *
 * Utilities - Functions and Classes common to the whole phpMyFAQ architecture
 *
 * @author    Thorsten Rinne <thorsten@phpmyfaq.de>
 * @author    Matteo Scaramuccia <matteo@scaramuccia.com>
 * @since     2005-11-01
 * @copyright (c) 2005-2007 phpMyFAQ Team
 *
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 */

// {{{ Constants
/**#@+
  * HTTP GET Parameters PMF accepted keys definitions
  */
define("HTTP_PARAMS_GET_CATID", "catid");
define("HTTP_PARAMS_GET_CURRENTDAY", "today");
define("HTTP_PARAMS_GET_DISPOSITION", "dispos");
define("HTTP_PARAMS_GET_GIVENDATE", "givendate");
define("HTTP_PARAMS_GET_LANG", "lang");
define("HTTP_PARAMS_GET_DOWNWARDS", "downwards");
define("HTTP_PARAMS_GET_TYPE", "type");
/**#@-*/
// }}}

// {{{ Classes
/**
 * PMF_Utils class
 *
 * This class has only static methods
 */
class PMF_Utils
{
    /**
     * Returns a phpMyFAQ date
     *
     * @param  integer $unixTime
     * @return string
     * @access public
     * @static
     */
    public static function getPMFDate($unixTime = NULL)
    {
        if (!isset($unixTime)) {
            // localtime
            $unixTime = time();
        }
        return date('YmdHis', $unixTime);
    }

    /**
     * Returns date from out of time
     *
     * @return string
     * @access public
     * @static
     */
    public static function getNeverExpireDate()
    {
        // Unix: 13 Dec 1901 20:45:54 -> 19 Jan 2038 03:14:07, signed 32 bit
        // Windows: 1 Jan 1970 -> 19 Jan 2038.
        // So we will use: 1 Jan 2038 -> 2038-01-01, 00:00:01
        return PMF_Utils::getPMFDate(mktime(0, 0 , 1, 1, 1, 2038));
    }

    /**
     * Check if a given digit is an integer
     *
     * @param  string $digits
     * @return bool
     * @access public
     * @static
     */
    public static function isInteger($digits)
    {
        return (preg_match("/^[0-9]+$/", $digits));
    }

    /**
     * Check if a given string could be a language
     *
     * @param  string $lang
     * @return bool
     * @access public
     * @static
     */
    public static function isLanguage($lang)
    {
        return (preg_match("/^[a-zA-Z\-]+$/", $lang));
    }

    /**
     * Checks if a date is a phpMyFAQ valid date
     *
     * @param  integer $date
     * @return integer
     * @access public
     * @static
     */
    public static function isLikeOnPMFDate($date)
    {
        // Test if the passed string is in the format: %YYYYMMDDhhmmss%
        $testdate = $date;
        // Suppress first occurence of "%"
        if (substr($testdate, 0, 1) == "%") {
            $testdate = substr($testdate, 1);
        }
        // Suppress last occurence of "%"
        if (substr($testdate, -1, 1) == "%") {
            $testdate = substr($testdate, 0, strlen($testdate)-1);
        }
        // PMF date consists of numbers only: YYYYMMDDhhmmss
        return (PMF_Utils::isInteger($testdate));
    }

    /**
     * Shortens a string for a given number of words
     *
     * @param   string  $str
     * @param   integer $char
     * @return  string
     * @access  public
     * @since   2002-08-26
     * @author  Thorsten Rinne <thorsten@phpmyfaq.de
     */
    public static function makeShorterText($str, $char)
    {
        $str = preg_replace('/\s+/', ' ', $str);
        $arrStr = explode(' ', $str);
        $shortStr = '';
        $num = count($arrStr);

        if ($num > $char) {
            for ($j = 0; $j <= $char; $j++) {
                $shortStr .= $arrStr[$j]." ";
            }
            $shortStr .= '...';
        } else {
            $shortStr = $str;
        }

        return $shortStr;
    }

    /**
     * Shuffles an associative array without losing key associations
     *
     * @param   array   $data
     * @return  array   $shuffled_data
     * @access  static
     * @since   2007-04-12
     * @author  Thorsten Rinne <thorsten@phpmyfaq.de
     */
    public static function shuffleData($data)
    {
        $shuffled_data = array();

        if (is_array($data)) {
            if (count($data) > 1) {
                $randomized_keys = array_rand($data, count($data));

                foreach($randomized_keys as $current_key) {
                    $shuffled_data[$current_key] = $data[$current_key];
                }
            } else {
                $shuffled_data = $data;
            }
        }

        return $shuffled_data;
    }
}
