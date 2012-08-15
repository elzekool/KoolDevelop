<?php
/**
 * PassTrough Translator
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage International
 **/

namespace KoolDevelop\International;

/**
 * PassTrough Translator
 *
 * Translator that just passes trough the untranslated strings. This
 * translator is used as failback if no other translator is choosen.
 *
 * @package KoolDevelop
 * @subpackage International
 */
class PassTroughTranslator implements \KoolDevelop\International\ITranslator
{
    /**
     * Initialize Translator
     *
     * @param string                     $domain          Domain
     * @param string                     $locale_settings Settings specific to current locale
     * @param \KoolDevelop\Configuration $configuration   All configuration options
     *
     * @return void
     */
    public function initialize($domain, $locale_settings, \KoolDevelop\Configuration $configuration) {
        // Nothing to do
    }

    /**
     * Translate a singular string
     *
     * This function just passes trough the untranslated string
     *
     * @param string $singular Untranslated string
     *
     * @return string Translated string
     */
    public function singular($singular) {
        return $singular;
    }

    /**
     * Translate a plural string
     *
     * This function passes the untranslated singular/plural strings trough
     * based on the english form ( count=1 ? singular : plural )
     *
     * @param string $singular Untranslated singular string
     * @param string $plural   Untranslated plural string
     * @param int    $count    Count
     *
     * @return string Translated string
     */
    public function plural($singular, $plural, $count) {
        return ($count == 1) ? $singular : $plural;
    }


}