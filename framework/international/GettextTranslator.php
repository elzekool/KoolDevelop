<?php
/**
 * Gettext Translator
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage International
 **/

namespace KoolDevelop\International;

/**
 * Gettext Translator
 *
 * Translator that uses Gettext .mo files for translation.
 * Filename for translation file is locale_settings[path]/<domain>.mo
 *
 * @package KoolDevelop
 * @subpackage International
 */
class GettextTranslator implements \KoolDevelop\International\ITranslator
{
    /**
     * Magic word form .MO header, Low Endian form
     * @ignore
     */
    const MAGIC_LOW_ENDIAN = "\xde\x12\x04\x95";

    /**
     * Magic word form .MO header, High Endian form
     * @ignore
     */
    const MAGIC_HIGH_ENDIAN = "\x95\x04\x12\xde";

    /**
     * Read header magic
     * @var int
     */
    private $HeaderMagic;

    /**
     * Current .MO file resource
     * @var resource
     */
    private $FileResource;

    /**
     * Translation table from .mo file
     * @var string[][]
     */
    private $TranslationTable = array();
    
    /**
     * Plural Form Selector
     * @var callable
     */
    private $PluralFormsSelector;
    
    /**
     * Reads a 32bit Integer from the Stream
     *
     * @return int Integer from stream
     */
    private function readInt32() {
        if ($this->HeaderMagic == self::MAGIC_LOW_ENDIAN) {
            $input = unpack('V', fread($this->FileResource, 4));
            return $input[1];
        } else {
            $input = unpack('N', fread($this->FileResource, 4));
            return $input[1];
        }
    }

    /**
     * Read entry from file
     *
     * @param int $offset Offset to string from offsetTable
     *
     * @return string String from entry
     */
    private function readEntry($offset) {
        if (fseek($this->FileResource, $offset['offset'], SEEK_SET) < 0) {
            return null;
        }
        if ($offset['size'] > 0) {
            return fread($this->FileResource, $offset['size']);
        }

       return '';
    }

    /**
     * Read array of 32-bit integers into an array
     *
     * @param string[] $keys Array keys
     *
     * @return int[] Integers
     */
    private function readInt32Array($keys) {
        $ints = array();
        foreach($keys as $key) {
            $ints[$key] = $this->readInt32();
        }
        return $ints;
    }

    /**
     * Parse the MO file header and returns the table
     * offsets
     *
     * @return int[] Offsets
     */
    private function parseHeader() {

        $this->HeaderMagic = fread($this->FileResource, 4);

        if ((self::MAGIC_LOW_ENDIAN != $this->HeaderMagic) AND (self::MAGIC_HIGH_ENDIAN != $this->HeaderMagic)) {
            return null;
        }

        $revision = $this->readInt32();
        if (0 != $revision) {
            return null;
        }

        $offsets = $this->readInt32Array(array("num_strings","orig_offset","trans_offset","hash_size","hash_offset"));
        return $offsets;

    }

    /**
     * Parse string offsets in a .mo table.
     *
     * This function parses the translations table and the originals table
     *
     * @param int      $offset Table offset
     * @param int      $num    Number of strings to parse
     *
     * @return int[][] Offsets table
     */
    private function parseOffsetTable($offset, $num) {
        if (fseek($this->FileResource, $offset, SEEK_SET) < 0) {
            return null;
        }
        $table = array();
        for ($i = 0; $i < $num; $i++) {
            $table[] = $this->readInt32Array(array("size","offset"));
        }
        return $table;
    }

    /**
     * Sanitize and transform Plural Expression
     *
     * @param string $expr Plural form expression
     * 
     * @return callable Plural form 
     */
    private function transformPluralExpression($expr) {
        
        $expr = preg_replace('@[^a-zA-Z0-9_:;\(\)\?\|\&=!<>+*/\%-]@', '', $expr);

        // Add parenthesis for tertiary '?' operator.
        $expr .= ';';
        $expr_length = strlen($expr);
        $php_expression = '';
        $p = 0;
        for ($i = 0; $i < $expr_length; $i++) {
            $ch = $expr[$i];
            switch ($ch) {
                case '?':
                    $php_expression .= ' ? (';
                    $p++;
                    break;
                case ':':
                    $php_expression .= ') : (';
                    break;
                case ';':
                    $php_expression .= str_repeat(')', $p) . ';';
                    $p = 0;
                    break;
                default:
                    $php_expression .= $ch;
            }
        }
        
        $php_expression = str_replace('nplurals', '$total', $php_expression);
        $php_expression = str_replace("n", '$count', $php_expression);
        $php_expression = str_replace('plural', '$pluralno', $php_expression);
        
        
        return $php_expression;
    }
    
    /**
     * Parse plural forms from header
     * 
     * @return void
     */
    private function parsePluralForms() {
        
        if (!isset($this->TranslationTable[''][0])) {
            $plural_php_expression = '$total=2; $pluralno = ($count == 1) ? 0 : 1;';
        } else if (preg_match("/(^|\n)plural-forms: ([^\n]*)\n/i", $this->TranslationTable[''][0], $matches)) {
            $plural_php_expression = $this->transformPluralExpression($matches[2]);
        } else {
            $plural_php_expression = '$total=2; $pluralno = ($count == 1) ? 0 : 1;';
        }
        
        $this->PluralFormsSelector = create_function(
            '$count',
            $plural_php_expression . '; if ($total <= $pluralno) { return $total - 1; } else { return $pluralno; }'
        );
                
    }


    
    /**
     * Parse the MO file
     *
     * @return void
     */
    private function parseMoFile($filename) {

        $filesize = filesize($filename);

        // Check if big enough for header
        if ($filesize < 4 * 7) {
            return;
        }

        $this->FileResource = fopen($filename, "rb");

        // Overflow protection
        $offsets = $this->parseHeader();
        if (null == $offsets || $filesize < 4 * ($offsets['num_strings'] + 7)) {
            fclose($this->FileResource);
            return;
        }

        // Read translation offset table
        if (null === ($translations_table = $this->parseOffsetTable($offsets['trans_offset'],$offsets['num_strings']))) {
            fclose($this->FileResource);
            return;
        }

        // Read translations
        $translations = array();
        foreach ($translations_table as $idx => $entry) {
            $translations[$idx] = $this->readEntry($entry);
        }

        // Read originals offset table
        $originals_table = $this->parseOffsetTable($offsets['orig_offset'], $offsets['num_strings']);

        // Go through originals
        foreach ($originals_table as $idx => $entry) {
            $entry = $this->readEntry($entry);
            $formes = explode(chr(0), $entry);
            $_translations = explode(chr(0), $translations[$idx]);
            foreach($formes as $form) {
                $this->TranslationTable[$form] = $_translations;
            }
        }

        $this->parsePluralForms();
        
        
        fclose($this->FileResource);
    }


    /**
     * Initialize Translator
     *
     * Use this function to initialize and configure your translator.
     * Locale settings provides the configuration data specific to the current locale
     * The full configuration file is also provided
     *
     * @param string                     $domain          Domain
     * @param string                     $locale_settings Settings specific to current locale
     * @param \KoolDevelop\Configuration $configuration   All configuration options
     *
     * @return void
     */
    public function initialize($domain, $locale_settings, \KoolDevelop\Configuration $configuration) {

        $mo_filename = $configuration->get('core.path', _APP_PATH_ . DS . 'international') . DS . $locale_settings['path'] . DS . $domain . '.mo';
        if (!file_exists($mo_filename)) {
            return;
        }

        $this->parseMoFile($mo_filename);

    }

    /**
     * Translate a singular string
     *
     * Translate a singular string and return translated string
     *
     * @param string $singular Untranslated string
     *
     * @return string Translated string
     */
    public function singular($singular) {
        if (isset($this->TranslationTable[$singular][0])) {
            return $this->TranslationTable[$singular][0];
        } else {
            return $singular;
        }
    }

    /**
     * Translate a plural string
     *
     * Translate a plural/singular string and return a translation based
     * on count. Altrough only singular and plural are provided as input
     * this function can (and should if appropiate) return more plural forms
     *
     * @param string $singular Untranslated singular string
     * @param string $plural   Untranslated plural string
     * @param int    $count    Count
     *
     * @return string Translated string
     */
    public function plural($singular, $plural, $count) {
        if (isset($this->TranslationTable[$singular])) {
            $pluralno = call_user_func($this->PluralFormsSelector, $count);
            if (isset($this->TranslationTable[$singular][$pluralno])) {
                return $this->TranslationTable[$singular][$pluralno];
            }
        }
        return ($count == 1) ? $singular : $plural;
    }

}