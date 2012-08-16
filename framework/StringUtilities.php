<?php
/**
 * String Utilities
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/

namespace KoolDevelop;

/**
 * String Utilities
 * 
 * Contains functions for manipulating strings
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
class StringUtilities 
{

    /**
     * Translation table
     * @var string[]
     */
    private static $ASCII = array(
        '/æ|ǽ/' => 'ae',
        '/œ/' => 'oe',
        '/À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ|А/' => 'A',
        '/à|á|â|ã|ä|å|ǻ|ā|ă|ą|ǎ|ª|а/' => 'a',
        '/Б/' => 'B',
        '/б/' => 'b',
        '/Ç|Ć|Ĉ|Ċ|Č|Ц/' => 'C',
        '/ç|ć|ĉ|ċ|č|ц/' => 'c',
        '/Ð|Ď|Đ|Д/' => 'Dj',
        '/ð|ď|đ|д/' => 'dj',
        '/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě|Е|Ё|Э/' => 'E',
        '/è|é|ê|ë|ē|ĕ|ė|ę|ě|е|ё|э/' => 'e',
        '/Ф/' => 'F',
        '/ƒ|ф/' => 'f',
        '/Ĝ|Ğ|Ġ|Ģ|Г/' => 'G',
        '/ĝ|ğ|ġ|ģ|г/' => 'g',
        '/Ĥ|Ħ|Х/' => 'H',
        '/ĥ|ħ|х/' => 'h',
        '/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ|И/' => 'I',
        '/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı|и/' => 'i',
        '/Ĵ|Й/' => 'J',
        '/ĵ|й/' => 'j',
        '/Ķ|К/' => 'K',
        '/ķ|к/' => 'k',
        '/Ĺ|Ļ|Ľ|Ŀ|Ł|Л/' => 'L',
        '/ĺ|ļ|ľ|ŀ|ł|л/' => 'l',
        '/М/' => 'M',
        '/м/' => 'm',
        '/Ñ|Ń|Ņ|Ň|Н/' => 'N',
        '/ñ|ń|ņ|ň|ŉ|н/' => 'n',
        '/Ö|Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ|О/' => 'O',
        '/ö|ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º|о/' => 'o',
        '/П/' => 'P',
        '/п/' => 'p',
        '/Ŕ|Ŗ|Ř|Р/' => 'R',
        '/ŕ|ŗ|ř|р/' => 'r',
        '/Ś|Ŝ|Ş|Ș|Š|С/' => 'S',
        '/ś|ŝ|ş|ș|š|ſ|с/' => 's',
        '/Ţ|Ț|Ť|Ŧ|Т/' => 'T',
        '/ţ|ț|ť|ŧ|т/' => 't',
        '/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ü|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ|У/' => 'U',
        '/ù|ú|û|ũ|ū|ŭ|ů|ü|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ|у/' => 'u',
        '/В/' => 'V',
        '/в/' => 'v',
        '/Ý|Ÿ|Ŷ|Ы/' => 'Y',
        '/ý|ÿ|ŷ|ы/' => 'y',
        '/Ŵ/' => 'W',
        '/ŵ/' => 'w',
        '/Ź|Ż|Ž|З/' => 'Z',
        '/ź|ż|ž|з/' => 'z',
        '/Æ|Ǽ/' => 'AE',
        '/ß/' => 'ss',
        '/Ĳ/' => 'IJ',
        '/ĳ/' => 'ij',
        '/Œ/' => 'OE',
        '/Ч/' => 'Ch',
        '/ч/' => 'ch',
        '/Ю/' => 'Ju',
        '/ю/' => 'ju',
        '/Я/' => 'Ja',
        '/я/' => 'ja',
        '/Ш/' => 'Sh',
        '/ш/' => 'sh',
        '/Щ/' => 'Shch',
        '/щ/' => 'shch',
        '/Ж/' => 'Zh',
        '/ж/' => 'zh'
    );

    /**
     * Transform CamelCased -> under_scored
     *
     * @param string $camelcased CamelCased string
     *
     * @return string under_scored string
     */
    public static function underscore($camelcased) {

        $underscored = "";
        $length = strlen($camelcased);

        // Transform
        for ($i = 0; $i < $length; $i++) {
            if ($i == 0) {
                $underscored .= strtolower($camelcased[$i]);
            } else if ($camelcased[$i] == '_') {
                $underscored .= '__';
            } else if (($camelcased[$i] >= 'A') && ($camelcased[$i] <= 'Z')) {
                $underscored .= '_' . strtolower($camelcased[$i]);
            } else {
                $underscored .= $camelcased[$i];
            }
        }
        return $underscored;
    }

    /**
     * Transform under_scored -> CamelCased
     *
     * @param string $underscored under_scored string
     *
     * @return string CamelCased string
     */
    public static function camelcase($underscored) {

        $camelcased = "";
        $uppercase = true;
        $length = strlen($underscored);

        // Transform
        for ($i = 0; $i < $length; $i++) {
            if ($uppercase) {
                $camelcased .= strtoupper($underscored[$i]);
                $uppercase = false;
                continue;
            } else if ($underscored[$i] == '_') {
                $uppercase = true;
                continue;
            } else {
                $camelcased .= $underscored[$i];
            }
        }

        return $camelcased;
    }

    /**
     * Transform string to 7-bit ASCII version
     *
     * @param string $s String
     *
     * @return string ASCII string
     */
    public static function ascii($s) {
        $s = preg_replace(array_keys(self::$ASCII), array_values(self::$ASCII), $s);
        return preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '', $s);
    }

}
