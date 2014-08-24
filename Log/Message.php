<?php
/**
 * Log Message
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/

namespace KoolDevelop\Log;

/**
 * Log Message
 *
 * Message Log by the Logger
 * 
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
class Message
{
    /**
     * Severe Message
     **/
    const LEVEL_SEVERE = 255;
    
    /**
     * Normal Message
     **/
    const LEVEL_NORMAL = 128;

    /**
     * Low priority (informational) Message
     **/
    const LEVEL_INFO = 0;
    
        /**
         * Message Level
         * @var int
         */
        private $Level;
        
        /**
         * DateTime 
         * @var \DateTime 
         */
        private $DateTime;
        
        /**
         * Message type
         * @var string
         */
        private $Type;
        
        /**
         * Message
         * @var string
         */
        private $Message;
        
        /**
         * Constructor
         * 
         * @param int $Level      Level
         * @param string $Type    Type
         * @param string $Message Message
         */
        function __construct($Level, $Type, $Message) {
            $this->Level = $Level;
            $this->Type = $Type;
            $this->Message = $Message;
            $this->DateTime = new \DateTime;                    
        }

        /**
         * Get Message Level
         * 
         * @return int Message Level
         */
        public function getLevel() {
            return $this->Level;
        }
        
        /**
         * Get DateTime Created
         * 
         * @return \DateTime DateTime Created
         */
        public function getDateTime() {
            return $this->DateTime;
        }
        
        /**
         * Get Message Type
         * 
         * @return string Message Type
         */
        public function getType() {
            return $this->Type;
        }

        /**
         * Get Message
         * 
         * @return string
         */
        public function getMessage() {
            return $this->Message;
        }

        /**
         * Get string representation of message
         * 
         * @return string
         */
        public function __toString() {
            return $this->getDateTime()->format('r') . ' | ' . sprintf('%03d', $this->getLevel()) . ' | ' . $this->getType() . ' | ' . $this->getMessage();
        }


}