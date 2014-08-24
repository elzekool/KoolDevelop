<?php
/**
 * E-mail message
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Email
 **/

namespace KoolDevelop\Email;

/**
 * E-mail message
 *
 * E-mail messaging class. Use this class to send e-mail messages. This class
 * uses PHPMailer internaly
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
     * Mailer implementation
     * @var \KoolDevelop\Email\IMailer
     */
    private static $Mailer;

    /**
     * Subject
     * @var string
     */
    private $Subject;

    /**
     * Setup new Mailer
     *
     * @param \KoolDevelop\Email\IMailer $Mailer Mailer
     */
    public static function setMailer(\KoolDevelop\Email\IMailer &$Mailer) {
        self::$Mailer = $Mailer;
    }


    /**
     * Constructor
     */
    function __construct() {
        if (self::$Mailer === null) {
            self::$Mailer = new PhpMailerMailer();
        }
    }

        /**
     * Check if given e-mailadres is valid
     *
     * @param string $emailaddress Address to check
     *
     * @return boolean Address valid
     */
    public static function checkEmailValid($emailaddress) {
        if (false === filter_var($emailaddress, FILTER_VALIDATE_EMAIL)) {
            return false;
        } else {
            return true;
        }
    }


    /**
     * Set From address
     *
     * @param string $address Address
     * @param string $name    Name
     *
     * @return \KoolDevelop\Email\Message Self
     */
    public function setFrom($address, $name) {
        if (static::checkEmailValid($address) AND !empty($name)) {
            self::$Mailer->setFrom($address, $name);
        } else {
            throw new \KoolDevelop\Exception\EmailException(__f('Invalid From e-mailaddress'));
        }
        return $this;
    }

    /**
     * Set Reply-To
     *
     * @param string $address Address
     * @param string $name    Name
     *
     * @return \KoolDevelop\Email\Message Self
     */
    public function setReplyTo($address, $name) {
        if (static::checkEmailValid($address) AND !empty($name)) {
            self::$Mailer->setReplyTo($address, $name);
        } else {
            throw new \KoolDevelop\Exception\EmailException(__f('Invalid ReplyTo e-mailaddress'));
        }
        return $this;
    }

    /**
     * Add Blind Carbon Copy Recipient
     *
     * @param string $address Address
     * @param string $name    Name
     *
     * @return \KoolDevelop\Email\Message Self
     */
    public function addBCC($address, $name) {
        if (self::checkEmailValid($address) AND !empty($name)) {
            self::$Mailer->addBCC($address, $name);
        } else {
            throw new \KoolDevelop\Exception\EmailException(__f('Invalid BCC e-mailaddress'));
        }
        return $this;
    }

    /**
     * Add Recipient
     *
     * @param string $address Address
     * @param string $name    Name
     *
     * @return \KoolDevelop\Email\Message Self
     */
    public function addTo($address, $name) {
        if (self::checkEmailValid($address) AND !empty($name)) {
            self::$Mailer->addTo($address, $name);
        } else {
            throw new \KoolDevelop\Exception\EmailException(__f('Invalid To e-mailaddress'));
        }
        return $this;
    }

    /**
     * Add attachment
     *
     * @param string $filename Filename (full path)
     * @param string $name     Name
     *
     * @return \KoolDevelop\Email\Message Self
     */
    public function addAttachment($filename, $name = null) {
        if (!file_exists($filename)) {
            throw new \KoolDevelop\Exception\EmailException(__f('Attachment file does not exist'));
        }
        self::$Mailer->addAttachment($filename, $name);
        return $this;
    }

    /**
     * Set Subject
     *
     * @param string $subject Subject
     *
     * @return \KoolDevelop\Email\Message
     */
    public function setSubject($subject) {
        $this->Subject = $subject;
        self::$Mailer->setSubject($subject);
        return $this;
    }

    /**
     * Set Contents of E-mail Message
     *
     * @param string  $message_view View file to use
     * @param mixed[] $parameters   Parameters
     *
     * @return \KoolDevelop\Email\Message
     */
    public function setContents($message_view, $parameters = array()) {
        $email_view = new \EmailView();
        $email_view->setView($message_view);
        $email_view->setTitle($this->Subject);
        foreach($parameters as $parameter => $value) {
            $email_view->set($parameter, $value);
        }        
        ob_start();
        $email_view->render();
        self::$Mailer->setContents(ob_get_clean());
        return $this;
    }

    /**
     * Reset settings, allowing to send new message
     *
     * @return void
     */
    public function reset() {
        self::$Mailer->reset();
    }
    
    /**
     * Send E-mail
     * 
     * @return void
     */
    public function send() {
        self::$Mailer->send();
    }
    
}
