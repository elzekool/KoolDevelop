<?php
/**
 * Not Found Exception
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/

namespace KoolDevelop\Exception;

/**
 * Not Found Exception
 *
 * Use this Exception to indicate that a resource could not be found.
 * The ErrorHandler can be set up to trigger a 404 Not Found response on throwing
 * this Exception.
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
class NotFoundException extends \KoolDevelop\Exception\Exception
{

}