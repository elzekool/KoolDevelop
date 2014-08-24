<?php
/**
 * Translator Interface
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage International
 **/

namespace KoolDevelop\International;

/**
 * Translator Interface
 *
 * Interface used by I18n for translating texts. You can create your own
 * translator by implementing this interface. Set the classname to the
 * core.translator setting in international.ini
 *
 * @package KoolDevelop
 * @subpackage International
 */
interface ITranslator
{
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
    public function initialize($domain, $locale_settings, \KoolDevelop\Configuration $configuration);

    /**
     * Translate a singular string
     *
     * Translate a singular string and return translated string
     *
     * @param string $singular Untranslated string
     *
     * @return string Translated string
     */
    public function singular($singular);

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
    public function plural($singular, $plural, $count);


}