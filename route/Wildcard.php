<?php
/**
 * Wildcard expression route
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/

namespace KoolDevelop\Route;

/**
 * Wildcard expression route
 *
 * Wildcard. Matches routes/URI's on wildcard expressions. Use a questionmark for
 * single characters and an asterix for a group of characters. An asterix at the end
 * of the expression matches the remainder.
 * 
 * Use $1, $2.. to use the captured value of the wildcard expressions
 * URI/route.
 * 
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
class Wildcard extends \KoolDevelop\Route\RegEx
{

    /**
	 * Constructor
	 *
	 * @param string  $in   Wildcard expression to match
	 * @param string  $out  Output on match (You can use $1, $2.. to get wildcard matches)
	 * @param boolean $stop Stop on match
	 */
    public function __construct($in, $out, $stop = false) {
                
        $expression = '/^';
        
        // Go trough characters
        foreach(str_split($in) as $pos => $c) {
            
            // Match meta characters
            if (in_array($c, array('/', '\\', '$', '^', '.', '[', ']', '(', ')', '+', '{', '}', '-'))) {
                $expression .= '\\' . $c;
                
            // Match on question mark
            } else if ($c == '?') {
                $expression .= '(.)';
                
            // Match on wildcard
            } else if ($c == '*') {
                
                // Check if last
                if ($pos == (strlen($in) - 1)) {
                    $expression .= '(.+)$';
                } else {
                    $expression .= '([^\\/]+)';
                }
                
            } else {
                $expression .= $c;
            }
            
        }
        
        $expression .= '/';
        
        parent::__construct($expression, $out, $stop);
    }

}
