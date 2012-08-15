<?php
/**
 * Framework Entry point
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Console
 **/

namespace KoolDevelop\Console;


/**
 * Console Application
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Console
 **/
final class Console extends \KoolDevelop\Observable
{

    /**
     * Console instance
     * @var \KoolDevelop\Console\Console
     */
    private static $Instance;

    /**
     * Get KoolDevelop\Console\Console instance
     *
     * @return KoolDevelop\Console\Console
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
    private function __construct() {
        $this->addObservable('onException');

        // Add default callback
        $this->addObserver('onException', function(\Exception $e) {
            throw $e;
        });

    }

    /**
     * Start command
     *
     * @param \KoolDevelop\Console\ITask $task      Task
     * @param string                     $command   Command
     * @param string[]                   $arguments Arguments
     *
     * @return void
     */
	private function runAction(\KoolDevelop\Console\ITask &$task, $command, $arguments) {

		// Check if required parameters are set
	 	$class_reflection = new \ReflectionClass($task);
	 	$method_reflection = $class_reflection->getMethod($command);

	 	$required_parameters = $method_reflection->getNumberOfRequiredParameters();
	 	$given_parameters = count($arguments);

	 	if ($required_parameters > $given_parameters) {
	 		throw new \InvalidArgumentException(__f("Command requires " . $required_parameters . " parameters, " . $given_parameters . " given",'kooldevelop'));
	 	}

		switch (count($arguments)) {
			case 0:
				$task->$command();
				break;
			case 1:
				$task->$command($arguments[0]);
				break;
			case 2:
				$task->$command($arguments[0], $arguments[1]);
				break;
			case 3:
				$task->$command($arguments[0], $arguments[1], $arguments[2]);
				break;
			case 4:
				$task->$command($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
				break;
			case 5:
				$task->$command($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4]);
				break;
			default:
				call_user_func_array(array(&$task, $command), $arguments);
				break;
		}

	}

    /**
     * Start Console Application
     *
     * @return void
     */
    public function start() {

        \KoolDevelop\Configuration::setCurrentEnvironment('console');

        // Load Bootstrapper
        $bootstrapper = new \Bootstrapper();
        $bootstrapper->console();

        try {

            $task = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : null;
            $command = isset($_SERVER['argv'][2]) ? $_SERVER['argv'][2] : 'index';
            $arguments = array_splice($_SERVER['argv'], 3);

            if ($task === null) {
                throw new \InvalidArgumentException(__f("No task given, usage:\nphp console.php <task> <command> [arguments]",'kooldevelop'));
            }

            // Try to load task, first from application then from framework
            if (class_exists('\\Console\\' . $task . 'Task')) {
                $classname = '\\Console\\' . $task . 'Task';
            } else if (class_exists('\\KoolDevelop\\Console\\' . $task . 'Task')) {
                $classname = '\\KoolDevelop\\Console\\' . $task . 'Task';
            } else {
                throw new \InvalidArgumentException(__f("Task not found, usage:\nphp console.php <task> <command> [arguments]",'kooldevelop'));
            }

            // Make sure class is a task, not some arbritary class
            $task = new $classname();
            if (!($task instanceof \KoolDevelop\Console\ITask)) {
                throw new \InvalidArgumentException(__f("Class not instance of \KoolDevelop\Console\Task, make sure class is meant as task",'kooldevelop'));
            }

            $this->runAction($task, $command, $arguments);


        } catch(\Exception $e) {
            $this->fireObservable('onException', true, $e);
        }

    }


}
