<?php
/**
 * Console Task interface
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Console
 **/

namespace KoolDevelop\Console;


/**
 * Console Task interface
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Console
 **/
class LocaleTask implements \KoolDevelop\Console\ITask
{
    /**
     * Default command
     *
     * @return void
     */
    public function index() {
        $this->execute(_APP_PATH_);
    }

    /**
	 * Base Path
	 *
	 * @var string
	 */
	private $basepath;

	/**
	 * Regular Expression of files to parse
	 *
	 * @var string
	 */
	private $files_to_parse = '/^[^\.]{1}(.*)\.php$/';

	/**
	 * Regular Expression of files to skip
	 *
	 * @var string
	 */
	private $files_to_exclude = '/^$/';

	/**
	 * Regular Expression of folders to parse
	 *
	 * @var string
	 */
	private $dirs_to_parse = '/^[^\.]{1}(.*)$/';

	/**
	 * Regular Expression of files to skip
	 * @var string
	 */
	private $dirs_to_exclude = '/^(tests|webroot)(.*)/';

	/**
	 * Template generation start
	 *
	 * @param string $basepath Base Path
	 *
	 * @return void
	 */
	private function execute($basepath) {

		$this->basepath = $basepath;

		$matches = $this->processFolder('/');

		$matches_p_domain = array();
		foreach($matches as &$match) {
			if (!isset($matches_p_domain[$match['domain']])) {
				$matches_p_domain[$match['domain']] = array();
			}
			$matches_p_domain[$match['domain']][] = $match;
		}

		foreach($matches_p_domain as $domain => $domain_matches) {

			$output  = "# LANGUAGE Translations\n";
			$output .= "# Copyright YEAR NAME <EMAIL@ADDRESS>\n";
			$output .= "# --VERSIONS--\n";
			$output .= "#\n";
			$output .= "#, fuzzy\n";
			$output .= "msgid \"\"\n";
			$output .= "msgstr \"\"\n";
			$output .= "\"Project-Id-Version: PROJECT VERSION\\n\"\n";
			$output .= "\"POT-Creation-Date: " . date("Y-m-d H:iO") . "\\n\"\n";
			$output .= "\"PO-Revision-Date: YYYY-mm-DD HH:MM+ZZZZ\\n\"\n";
			$output .= "\"Last-Translator: NAME <EMAIL@ADDRESS>\\n\"\n";
			$output .= "\"Language-Team: LANGUAGE <EMAIL@ADDRESS>\\n\"\n";
			$output .= "\"MIME-Version: 1.0\\n\"\n";
			$output .= "\"Content-Type: text/plain; charset=utf-8\\n\"\n";
			$output .= "\"Content-Transfer-Encoding: 8bit\\n\"\n";
			$output .= "\"Plural-Forms: nplurals=2; plural= n == 1 ? 0 : 1;\\n\"\n\n";

			// Remove doubles
			$matches_without_doubles = array();
			$matches_singular = array();
			$matches_plural = array();

			foreach($domain_matches as $match) {
				$in_prev = false;
				switch($match['type']) {
					case 'singular':
						$in_prev = in_array($match['msgid'], $matches_singular);
						$matches_singular[] = $match['msgid'];
						break;
					case 'plural':
						$in_prev = in_array(array($match['msgid'], $match['msgid_plural']), $matches_plural);
						$matches_plural[] = array($match['msgid'], $match['msgid_plural']);
						break;
				}
				if ($in_prev == false) {
					$matches_without_doubles[] = $match;
				} else {
					switch($match['type']) {

						// Singular
						case 'singular':
							for($i = 0; $i < count($matches_without_doubles); $i++) {
								if ($matches_without_doubles[$i]['msgid'] == $match['msgid']) {
									$matches_without_doubles[$i]['ref'] .= ', ' . $match['ref'];
								}
							}
							break;

						// Plural
						case 'plural':
							for($i = 0; $i < count($matches_without_doubles); $i++) {
								if (($matches_without_doubles[$i]['msgid'] == $match['msgid']) AND ($matches_without_doubles[$i]['msgid_plural'] == $match['msgid_plural'])) {
									$matches_without_doubles[$i]['ref'] .= ', ' . $match['ref'];
								}
							}
							break;
					}
				}
			}

			foreach($matches_without_doubles as $match) {

				switch($match['type']) {

					// Singular
					case 'singular':
						$output .= "#: " . $match['ref'] . "\n";
						$output .= "msgid \"" . $match['msgid'] . "\"\n";
						$output .= "msgstr \"" . $match['msgstr'] . "\"\n";
						$output .= "\n";
						break;

					// Plural
					case 'plural':
						$output .= "#: " . $match['ref'] . "\n";
						$output .= "msgid \"" . $match['msgid'] . "\"\n";
						$output .= "msgid_plural \"" . $match['msgid_plural'] . "\"\n";
						$output .= "msgstr[0] \"" . $match['msgstr[0]'] . "\"\n";
						$output .= "msgstr[1] \"" . $match['msgstr[1]'] . "\"\n";
						$output .= "\n";
						break;
				}
			}

			file_put_contents($this->basepath . '/international/' . $domain . '.pot', $output);

		}



	}

	/**
	 * Proces folder and return found matches
	 *
	 * @param string $folder Folder
	 *
	 * @return array Matches
	 */
	private function processFolder($folder) {

		$matches = array();

		if (!is_dir($this->basepath . $folder)) {
			throw new \InvalidArgumentException(__f("Folder does not exsist.",'kooldevelop'));
		}

		if (false !== ($handle = opendir($this->basepath . $folder))) {

			while (($folder_item = readdir($handle)) !== false) {

				if (is_dir($this->basepath . $folder . $folder_item) AND (preg_match($this->dirs_to_parse, $folder_item) > 0)  AND (preg_match($this->dirs_to_exclude, $folder_item) == 0)) {
					$foldermatches = $this->processFolder($folder . $folder_item . '/');
					$matches = array_merge($matches, $foldermatches);

				} else if (is_file($this->basepath . $folder . $folder_item) AND (preg_match($this->files_to_parse, $folder_item) > 0)  AND (preg_match($this->files_to_exclude, $folder_item) == 0)) {
					$filematches = $this->processFile($folder . $folder_item);
					$matches = array_merge($matches, $filematches);
				}
			}
		}

		return $matches;
	}

	/**
	 * Proces file and return found matches
	 *
	 * @param string $filename Filename
	 *
	 * @return array Matches
	 */
	private function processFile($filename) {

		if (!is_file($this->basepath . $filename)) {
			throw new \InvalidArgumentException(__f("File does not exsist.",'kooldevelop'));
		}

		echo $filename . "\n";

		if (strlen($file_contents = file_get_contents($this->basepath . $filename)) > 0) {
			$tokens = token_get_all($file_contents);
			if (count($tokens) > 0) {
				return $this->parseTokens($tokens, $filename);
			}
		}

		return array();
	}


	/**
	 * Proces function parameters
	 *
	 * @param int     $offset Offset of function
	 * @param mixed[] $tokens Tokens
	 *
	 * @return parameters
	 */
	private function parseFunction($offset, &$tokens) {

		$depth = -1;
		$pos = $offset;
		$strings = array();

		while (!(($tokens[$pos] == ')') AND ($depth == 0))) {

			$token = $tokens[$pos];
			$pos++;

			if ($token == '(') {
				$depth++;
				continue;
			} else if ($token == ')') {
				$depth--;
				continue;
			} else if ($depth == 0) {
				if (is_array($token) AND ($token[0] == T_CONSTANT_ENCAPSED_STRING)) {
					$strings[] = $token[1];
				}
			}
			if ($pos == count($tokens)) {
				break;
			}
		}

		return $strings;
	}

	/**
	 * Loop trough tokens and return array of matches
	 *
	 * @param mixed[] $tokens   Tokens
	 * @param string  $filename Filename for referente
	 *
	 * @return array Matches
	 */
	private function parseTokens(&$tokens, $filename) {

		$parseFunctions = array(
			"__"    => 'singular',
			"__e"   => 'singular',            
            "__w"   => 'singular',
            "__f"   => 'singular',
            
			"__n"   => 'plural',            
			"__en"  => 'plural',
            "__ew"  => 'plural',                        
            "__fn"  => 'plural'
		);

		$matches = array();

		$tokenCount = count($tokens);

		for($i = 0; $i < ($tokenCount - 3); $i++) {

			if ((@$tokens[$i][0] == T_STRING)) {
				if ((@$tokens[$i+1] == '(')) {

					if (isset($parseFunctions[$tokens[$i][1]])) {
						$params = $this->parseFunction($i, $tokens);
						$type = $parseFunctions[$tokens[$i][1]];

						// Check if we have enough parameters
						if (
							(($type == 'singular') AND (count($params) >= 1)) OR
							(($type == 'plural') AND (count($params) >= 2))
						) {

							if ($type == 'plural') {
								$matches[] = array(
									'type' 			=> $type,
									'domain'        => isset($params[2]) ? substr($params[2], 1, -1) : 'default',
									'ref'			=> $this->formatString('"'. $filename . ':' . $tokens[$i][2]. '"'),
									'msgid' 		=> $this->formatString($params[0]),
									'msgid_plural' 	=> $this->formatString($params[1]),
									'msgstr[0]'		=> $this->formatString($params[0]),
									'msgstr[1]'		=> $this->formatString($params[1])
								);
							} else {
								$matches[] = array(
									'type' 			=> $type,
									'domain'        => isset($params[1]) ? substr($params[1], 1, -1) : 'default',
									'ref'			=> $this->formatString('"' . $filename . ':' . $tokens[$i][2] . '"'),
									'msgid' 		=> $this->formatString($params[0]),
									'msgstr'		=> $this->formatString($params[0])

								);
							}

						}
					}


				}
			}
		}

		// Geef resultaten terug
		return $matches;

	}

	/**
	 * Convert string for PO file
	 *
	 * @param string $string Input string
	 *
	 * @return string String for PO file
	 */
	private function formatString($string) {
		$quote = substr($string, 0, 1);
		$string = substr($string, 1, -1);
		if ($quote == '"') {
			$string = stripcslashes($string);
		} else {
			$string = strtr($string, array("\\'" => "'", "\\\\" => "\\"));
		}
		$string = str_replace("\r\n", "\n", $string);
		return addcslashes($string, "\0..\37\\\"");
	}

}