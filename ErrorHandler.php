<?php
/**
 * Error Handler
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/

namespace KoolDevelop;

/**
 * Error Handler
 *
 * Base error handler. This class is used for handling exceptions / recoverable errors.
 * You can prepend or replace the default onError observer with your own.
 * 
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
class ErrorHandler extends \KoolDevelop\Observable implements \KoolDevelop\Configuration\IConfigurable
{
    /**
     * Singleton Instance
     * @var \KoolDevelop\ErrorHandler
     */
    protected static $Instance;

    /**
     * Get KoolDevelopRouter instance
     *
     * @return \KoolDevelop\ErrorHandler
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
        $this->addObservable('onError');

        $config = \KoolDevelop\Configuration::getInstance('core');

        // Setup error reporting
        ini_set('display_errors', $config->get('errors.display_errors', 0));
        error_reporting($config->get('errors.error_reporting', E_ALL));

        // Trigger Exception on PHP Error
        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            if (($errno & error_reporting()) != 0) {
                throw new \ErrorException(__f($errstr, 0, $errno, $errfile, $errline,'kooldevelop'));
            }
        });

        // Add default observer
        $this->addObserver('onError', function(\Exception $e) {
            echo '<!DOCTYPE html PUBLIC>' . "\n";
            echo '<html>';
            echo '<head><title>' . htmlspecialchars($e->getMessage()) . '</title></head>';
            echo '<body style="background: #500;">';            
            echo '<div style="font-family: arial; color: #333; font-size: 14px; margin: 50px auto; width: 800px; background: #fff; padding: 20px; border-radius: 5px;">';
            echo '<h1 style="margin: 10px 0 0 0;">' . htmlspecialchars($e->getMessage()) . '</h1>';
            echo '<h3 style="margin: 5px 0 0 0; color: #777; ">' . htmlspecialchars(get_class($e)) . '</h3>';

            echo '<p>An error occurred. Please try again later. Our apologies.</p>';

            if (\KoolDevelop\Configuration::getInstance('core')->get('errors.display_stacktrace', 1) == 1) {
                echo '<div style="font-family: monospace; font-size: 12px; background: #eee; padding: 1em; width: 90%; border: 1px dashed #999; margin: 0 0 2em 0;">';
                echo '<b>Stack trace:</b>';
                echo '<ol>';
                foreach ($e->getTrace() as $trace_no => $trace_item) {
                    echo '<li>';
                    if (isset($trace_item['file'])) {
                        echo strlen($trace_item['file']) < 30 ? $trace_item['file'] : '...' . substr($trace_item['file'], -30);
                        echo ':';
                        echo $trace_item['line'];
                    } else {
                        echo strlen($e->getFile()) < 30 ? $e->getFile() : '...' . substr($e->getFile(), -30);
                        echo ': ';
                        echo $e->getLine();
                    }
                    echo ' ';
                    echo isset($trace_item['class']) ? $trace_item['class'] . ':' . $trace_item['function'] : $trace_item['function'] . '()';
                    echo "</li>";
                }
                echo '</ol>';
                echo '</div>';
            }
            
            if (\KoolDevelop\Configuration::getInstance('core')->get('errors.display_details', 1) == 1) {
                if ($e instanceof \KoolDevelop\Exception\Exception) {
                    echo '<div style="font-family: monospace; font-size: 12px; background: #eee; padding: 1em; width: 90%; border: 1px dashed #999; margin: 0 0 2em 0;">';
                        echo '<b>Details:</b><br />';
                        echo nl2br($e->getDetail());
                    echo '</div>';
                }
            }
            
            echo '</div>';
            echo '</body>';
            echo '</html>';
            die();
        });

    }

    /**
     * Handle Exception
     *
     * @param \Exception $e Exception
     */
    public function handleException(\Exception $e) {
        $this->fireObservable('onError', true, $e);
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
            new \KoolDevelop\Configuration\IConfigurableOption('core', 'errors.display_errors', '0', ('1 to show PHP errors, 0 to hide them')),
            new \KoolDevelop\Configuration\IConfigurableOption('core', 'errors.error_reporting', 'E_ALL & ~(E_DEPRECATED)', ('PHP Error types to handle')),
            new \KoolDevelop\Configuration\IConfigurableOption('core', 'errors.display_stacktrace', '0', ('1 to show stacktrace, 0 to hide them')),
            new \KoolDevelop\Configuration\IConfigurableOption('core', 'errors.display_details', '0', ('1 to show display details of KoolDevelop specific exceptions, 0 to hide them'))
        );
    }


}