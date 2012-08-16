<?php
/**
 * Localization
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage International
 **/

namespace KoolDevelop\International;

/**
 * Localization
 *
 * Localisation tools, find and react to the selected locale.
 * Used in combination with the internationalization class.
 *
 * @see \KoolDevelop\International\I18n
 * 
 * @package KoolDevelop
 * @subpackage International
 */
final class L10n
{
	/**
	 * Singleton Instance
	 * @var \KoolDevelop\International\L10n
	 */
	protected static $Instance;

	/**
	 * Get \KoolDevelop\International\L10n instance
	 *
	 * @return \KoolDevelop\International\L10n
	 */
	public static function getInstance() {
		if (self::$Instance === null) {
        	self::$Instance = new self();
      	}
      	return self::$Instance;
    }

	/**
	 * Current Locale
	 * @var string
	 */
	private $CurrentLocale;

    /**
     * Locale settings from international.ini
     * @var string[]
     */
    private $CurrentLocaleSettings;

	/**
	 * Locale mappings
	 * @var array
	 */
	private $Mappings;


	/**
	 * Get current Locale
	 *
	 * @return string Locale
	 */
	public function getLocale() {
		if ($this->CurrentLocale === null) {
			throw new \RuntimeException(__f("No locale found/setup",'kooldevelop'));
		}
		return $this->CurrentLocale;
	}

    /**
     * Get current Locale settings from international.ini
     *
     * Returns the settings from international.ini for the current
     * locale.
     *
     * @return string[] Locale settings
     */
    public function getLocaleSettings() {
        if ($this->CurrentLocaleSettings === null) {
			throw new \RuntimeException(__f("No locale found/setup",'kooldevelop'));
		}
		return $this->CurrentLocaleSettings;
    }

	/**
	 * Constructor
	 */
	private function __construct() {

		$configuration = \KoolDevelop\Configuration::getInstance('international');
		$session = \KoolDevelop\Session::getInstance();

		$session_id = $configuration->get('core.session_id', 'lang');
		$session_storage = $configuration->get('core.session_handler', 'Php');
		$session_timeout = $configuration->get('core.session_timeout', 0);

		// Load Mappings
		$this->Mappings = $this->loadMappings();

		// Check if language is given
		$get_param = $configuration->get('core.get_param', 'lang');
		if (isset($_GET[$get_param])) {
			$langs = array(
				$_GET[$get_param] => 1
			);
			$session->set($session_id, $_GET[$get_param], $session_timeout, $session_storage);
		} else if ($session->exists($session_id, $session_storage)) {
			$session_language = $session->get($session_id, null, $session_storage);
			$langs = array(
				$session_language => 1
			);
		} else {
			$langs = $this->getBrowserLanguages();
		}

		$found_mapping = null;

		foreach($langs as $lang => $factor) {
			foreach($this->Mappings as $mapping_naam => $mapping_info) {
				if (preg_match($mapping_info['regex'], $lang) > 0) {
					$found_mapping = $mapping_info;
					break;
				}
			}
			if ($found_mapping != null) {
				break;
			}
		}

		if ($found_mapping === null) {
			$found_mapping = $this->Mappings[0];
		}

        $this->CurrentLocaleSettings = $found_mapping['settings'];
		$this->CurrentLocale = $found_mapping['locale'];
		setlocale(LC_ALL, $this->CurrentLocale);

	}

	/**
	 * Load mappings configuration
	 *
	 * @return mixed[] Mappings
	 */
	public function loadMappings() {


		$configuration = \KoolDevelop\Configuration::getInstance('international');
		$languages = $configuration->get('core.languages', 'nl');

		$mappings = array();
		foreach(explode(',', $languages) as $language) {
			$mappings[] = array(
				'regex' => $configuration->get($language . '.regex', ''),
				'locale' => $configuration->get($language . '.locale', ''),
                'settings' => $configuration->get($language,  array())
			);
		}

		// Mappings teruggeven
		return $mappings;
	}

	/**
	 * Get Browser Langage Preference
	 *
	 * @return int[] Key is language locale, value is preference
	 */
	protected function getBrowserLanguages() {
		$langs = array();
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    		preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);
    		if (count($lang_parse[1])) {
				foreach($lang_parse[1] as $index => $val) {
				  $lang_parse[1][$index] = str_replace('-','_',strtolower($val));
				}
        		$langs = array_combine($lang_parse[1], $lang_parse[4]);
        		foreach ($langs as $lang => $val) {
            		if ($val === '') $langs[$lang] = 1;
        		}
        		arsort($langs, SORT_NUMERIC);
    		} else {
    			$langs = array(
					'nl_nl' => 1
				);
    		}
		} else {
			$langs = array(
				'nl_nl' => 1
			);
		}
		return $langs;
	}

}

?>