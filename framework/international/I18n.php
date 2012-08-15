<?php
/**
 * Internationalization tools
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage International
 **/

namespace KoolDevelop\International;

/**
 * Internationalization tools
 *
 * Provides functions for translating texts using gettext translation files.
 * Uses L10n class to determine wich locale should be used. Use one of the provided
 * Translators or use your own. Fails back to the PassTroughTranslator function
 *
 * @package KoolDevelop
 * @subpackage International
 */
final class I18n
{
    /**
     * Translator
     * @var \KoolDevelop\International\ITranslator
     */
    private $Translator;

	/**
	 * Singleton Instances
	 * @var \KoolDevelop\International\I18n[]
	 */
	protected static $Instances = array();

	/**
	 * Get \KoolDevelop\International\I18n instance
	 *
	 * @return \KoolDevelop\International\I18n
	 */
	public static function getInstance($domain) {
		if (!isset(self::$Instances[$domain])) {
        	self::$Instances[$domain] = new self($domain);
      	}
      	return self::$Instances[$domain];
    }

    /**
	 * Constructor
	 */
	protected function __construct($domain) {
        $configuration = \KoolDevelop\Configuration::getInstance('international');
        $locale_settings = \KoolDevelop\International\L10n::getInstance()->getLocaleSettings();
        $translator = $configuration->get('core.translator', '\\KoolDevelop\\International\\PassTroughTranslator');

        $this->Translator = new $translator();
        $this->Translator->initialize($domain, $locale_settings, $configuration);
	}

	/**
	 * Translate text
     *
	 * @param string $text Untranslated text
	 *
	 * @return string Translated tekst
	 */
	function singular($text) {
        return $this->Translator->singular($text);
	}

	/**
	 * Translate text based on count, $count dictates wich version is used
	 *
	 * @param string $singular Singular untranslated text
	 * @param string $plural   Plural untranslated text
	 * @param int    $count    Count to determine singular/plural
	 *
	 * @return string Translated tekst
	 */
	function plural($singular, $plural, $count) {
		return $this->Translator->plural($singular, $plural, $count);
	}


}
