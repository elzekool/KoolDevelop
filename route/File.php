<?php
/**
 * File Route
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/

namespace KoolDevelop\Route;

/**
 * File Route
 *
 * Allows loading files from outside the webroot. On a match further processing
 * of rules and loading of the application is always stopped.
 * 
 * Inportant notice to this route element. Make sure you make your matches save when
 * using pattern matches in your filename. For example only allow a-z0-9
 * 
 * <code>
 * $this->Router->addRoute(new KoolDevelop\Route\File(
 *   '/^\/w\/([a-zA-Z0-9_]+)\.html$/',
 *   APP_PATH . DS . 'libs' . DS . 'WebrootTest' . DS . '$1.html',
 *   array(
 *     'Content-Type: text/plain'
 *   )
 * ));
 * </code>
 * 
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
class File implements \KoolDevelop\Route\IRoute
{
    /**
     * Regular expression to match
     * @var string
     */
    private $In;

    /**
     * Filename to search
     * @var string
     */
    private $Filename;

    /**
     * Headers to send on match
     * @var string[]
     */
    private $Headers;

    /**
     * Constructor
     *
     * @param string  $in       Regular expression to match (Make sure you make the routes save!)
     * @param string  $filename Filename to send (You can use $1, $2.. to get pattern matches)
     * @param boolean $headers  Headers to send on match (for ex. Content-Type)
     */
    public function __construct($in, $filename, $headers = array()) {
        $this->In = $in;
        $this->Filename = $filename;
        $this->Headers = $headers;
    }

    /**
     * Proces routing
     *
     * @param string $route Reference to current route
     *
     * @return boolean Stop further processing
     */
    public function route(&$route) {
        $matches = array();

        // Check match
        if (preg_match($this->In, $route, $matches) > 0) {           
            $filename = $this->Filename;
            
            foreach ($matches as $match_id => $match_value) {
                $filename = str_replace('$' . $match_id, $match_value, $filename);
            }
            
            // Convert to realpath
            if (false === ($filename = realpath($filename))) {
                return false;                
            // Check if exists
            } else if (false === file_exists($filename)) {
                return false;                
            // Check if even a file
            } else if (false === is_file($filename)) {
                return false;                
            }
            
            foreach($this->Headers as $header) {
                header($header);
            }
            
            // End all output buffering
            while(@ob_end_clean());
            
            // Readfile and exit
            readfile($filename);
            die();
            
        }

        return false;
    }

}
