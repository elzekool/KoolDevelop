<?php
/**
 * Logger
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/

namespace KoolDevelop\Log;

/**
 * Logger
 *
 * Application Logger. Used for logging messages from different parts of the framework
 * and the application.
 * 
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
class Logger implements \KoolDevelop\Configuration\IConfigurable
{    
    /**
     * Singleton Instance
     * @var \KoolDevelop\Log\Logger
     */
    protected static $Instance;

    /**
     * Log level (Scale: 0-255)
     * @var int
     **/
    private $LogLevel = 255;
    
    /**
     * Log Messages
     * @var \KoolDevelop\Log\Message[]
     */
    private $Messages = array();
    
    /**
     * Get Logger instance
     *
     * @return \KoolDevelop\Log\Logger
     */
    public static function getInstance() {
        if (self::$Instance === null) {
            self::$Instance = new self();
        }
        return self::$Instance;
    }

    /**
     * Constructor
     */
    protected function __construct() {
        $config = \KoolDevelop\Configuration::getInstance('core');
        $this->LogLevel = $config->get('logging.level', E_ALL);
     }
    
    /**
     * Log Message
     * 
     * @param int    $level   Level (0-255)
     * @param string $message Message
     * @param string $type    Type
     */
    public function log($level, $message, $type = '') {
        if ($level < $this->LogLevel) { 
            return;
        }
        $this->Messages[] = new Message($level, $type, $message);
    }
        
    /**
     * Log Severe Message
     * 
     * @see \KoolDevelop\Log\Logger::log()
     * 
     * @param string $message Message
     * @param string $type    Type
     */
    public function severe($message, $type = '') {
        $this->log(Message::LEVEL_SEVERE, $message, $type);
    }
    
    /**
     * Log normal priority Message
     * 
     * @see \KoolDevelop\Log\Logger::log()
     * 
     * @param string $message Message
     * @param string $type    Type
     */
    public function normal($message, $type = '') {
        $this->log(Message::LEVEL_NORMAL, $message, $type);
    }    
    
    /**
     * Log low (informational) priority Message
     * 
     * @see \KoolDevelop\Log\Logger::log()
     * 
     * @param string $message Message
     * @param string $type    Type
     */
    public function low($message, $type = '') {
        $this->log(Message::LEVEL_INFO, $message, $type);
    }
    
    /**
     * Get Messages
     * 
     * @param boolean $clear Clear log
     * 
     * @return \KoolDevelop\Log\Message[] Messages
     */
    public function getMessages($clear = true) {
        $messages = $this->Messages;
        if ($clear) {
            $this->Messages = array();
        }
        return $messages;
    }
    
    /**
     * Get list of (configurable) classes that this class
     * depends on. 
     * 
     * @return string[] Depends on
     */
    public static function getDependendClasses() {
        return array();
    }
    

    /**
     * Get Configuration options for this class
     * 
     * @return \KoolDevelop\Configuration\IConfigurableOption[] Options for class
     */
    public static function getConfigurationOptions() {      
        return array(
            new \KoolDevelop\Configuration\IConfigurableOption('core', 'logging.level', '255', ('Log level, 0-255. 0 log all message, 255 log only severe messages'))
        );
    }


}