<?php
/**
 * Shorthand functions
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/

/**
 * Get Router
 * 
 * Shorthand function to return Router instance
 * 
 * @return \KoolDevelop\Router Router
 */
function r() {
    return \KoolDevelop\Router::getInstance();
}


/**
 * Translate singular text
 *
 * @param Text   $text   Original Text
 * @param Domain $domain Domain
 *
 * @return Translated Text
 */
function __($text, $domain = 'default') {
	$i18n = \KoolDevelop\International\I18n::getInstance($domain);
	return $i18n->singular($text);
}

/**
 * Translate and Echo singular text
 
 * @param Text   $text   Original Text
 * @param Domain $domain Domain
 * 
 * @return void
 */
function __e($text, $domain = 'default') {
	$i18n = \KoolDevelop\International\I18n::getInstance($domain);
	echo $i18n->singular($text);
}

/**
 * Translate and Echo htmlspecialchar'd singular text
 
 * @param Text   $text   Original Text
 * @param Domain $domain Domain
 * 
 * @return void
 */
function __w($text, $domain = 'default') {
	$i18n = \KoolDevelop\International\I18n::getInstance($domain);
	echo htmlspecialchars($i18n->singular($text));
}

/**
 * Translate plural text
 *
 * @param string $singular Singular Text
 * @param string $plural   Plural Text
 * @param int    $count    Count
 * @param string $domain   Domain
 *
 * @return string Translated text
 */
function __n($singular, $plural, $count, $domain = 'default') {
	$i18n = \KoolDevelop\International\I18n::getInstance($domain);
	return $i18n->plural($singular, $plural, $count);
}

/**
 * Translate and echo plural text
 *
 * @param string $singular Singular Text
 * @param string $plural   Plural Text
 * @param int    $count    Count
 * @param string $domain   Domain
 *
 * @return void
 */
function __ne($singular, $plural, $count, $domain = 'default') {
	$i18n = \KoolDevelop\International\I18n::getInstance($domain);
	echo $i18n->plural($singular, $plural, $count);
}

/**
 * Translate and echo htmlspecialchar'd plural text
 *
 * @param string $singular Singular Text
 * @param string $plural   Plural Text
 * @param int    $count    Count
 * @param string $domain   Domain
 *
 * @return void
 */
function __nw($singular, $plural, $count, $domain = 'default') {
	$i18n = \KoolDevelop\International\I18n::getInstance($domain);
	echo htmlspecialchars($i18n->plural($singular, $plural, $count));
}

/**
 * Failsave translate singular text
 * 
 * This is a failsave version of the __() function. Use this function 
 * for error messages e.g. Use this function only when needed for performance.
 *
 * @param Text   $text   Original Text
 * @param Domain $domain Domain
 *
 * @return Translated Text
 */
function __f($text, $domain = 'default') {
	try {
        $i18n = \KoolDevelop\International\I18n::getInstance($domain);
        return $i18n->singular($text);
    } catch(\Exception $e) {
        // Ignore exception
        return $text;
    }
}


/**
 * Failsave translate plural text
 *
 * This is a failsave version of the __n() function. Use this function 
 * for error messages e.g. Use this function only when needed for performance.
 * 
 * @param string $singular Singular Text
 * @param string $plural   Plural Text
 * @param int    $count    Count
 * @param string $domain   Domain
 *
 * @return string Translated text
 */
function __fn($singular, $plural, $count, $domain = 'default') {
    try {
        $i18n = \KoolDevelop\International\I18n::getInstance($domain);
        return $i18n->plural($singular, $plural, $count);
    } catch(\Exception $e) {
        return ($count == 1) ? $singular : $plural;
    }
}
