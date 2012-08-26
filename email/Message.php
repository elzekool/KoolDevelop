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
 * Add PHPMailer
 * Old style lib that doesn't support PSR-0 style loading :(
 */
require_once FRAMEWORK_PATH . DS . 'libs' . DS . 'PHPMailer' . DS . 'class.phpmailer.php';

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
class Message implements \KoolDevelop\Configuration\IConfigurable
{

    /**
     * Internal PHPMailer object
     * @var \PHPMailer
     */
    private $PHPMailer;

    /**
     * From Mail Address
     * @var string
     */
    private $From;

    /**
     * From Name
     * @var string
     */
    private $FromName;

    /**
     * Reply-To Mail Address
     * @var string
     */
    private $ReplyTo;

    /**
     * Reply-To Name
     * @var string
     */
    private $ReplyToName;

    /**
     * Subject
     * @var string
     */
    private $Subject;

    /**
     * Blind Carbon Copy Recipients
     * @var string[]
     */
    private $BCC;

    /**
     * Recipients
     * @var string[]
     */
    private $To;

    /**
     * Contents
     * @var string
     */
    private $Contents;

    /**
     * Attachments
     * @var string[]
     */
    private $Attachments;

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
     * Constructor
     */
    public function __construct() {
        $this->reset();
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
            $this->From = $address;
            $this->FromName = $name;
        } else {
            throw new \KoolDevelop\Exception\Exception(__f('Invalid From e-mailaddress'));
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
            $this->ReplyTo = $address;
            $this->ReplyToName = $name;
        } else {
            throw new \KoolDevelop\Exception\Exception(__f('Invalid ReplyTo e-mailaddress'));
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
            $this->BCC[] = array($address, $name);
        } else {
            throw new \KoolDevelop\Exception\Exception(__f('Invalid BCC e-mailaddress'));
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
            $this->To[] = array($address, $name);
        } else {
            throw new \KoolDevelop\Exception\Exception(__f('Invalid To e-mailaddress'));
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
            throw new \KoolDevelop\Exception\Exception(__f('Attachment file does not exist'));
        }
        $this->Attachments[$filename] = ($name !== null ? $name : basename($filename));
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
        $this->Contents = ob_get_clean();
        return $this;
    }

    /**
     * Reset settings, allowing to send new message
     *
     * @return void
     */
    public function reset() {

        $configuration = \KoolDevelop\Configuration::getInstance('email');

        $this->Subject = '';
        $this->Contents = '';
        $this->BCC = array();
        $this->To = array();
        $this->Attachments = array();

        try {
            $this->PHPMailer = new \PHPMailer(true);

            // Check if we should enable SMTP
            if (null !== ($smtp_settings = $configuration->get('smtp', null))) {
                $this->PHPMailer->IsSMTP();
                if (isset($smtp_settings['host'])) {
                    $this->PHPMailer->Host = $smtp_settings['host'];
                }
                if (isset($smtp_settings['port'])) {
                    $this->PHPMailer->Port = $smtp_settings['port'];
                }
                if (isset($smtp_settings['username'])) {
                    $this->PHPMailer->Username = $smtp_settings['username'];
                    $this->PHPMailer->SMTPAuth = true;
                }
                if (isset($smtp_settings['password'])) {
                    $this->PHPMailer->Password = $smtp_settings['password'];
                }
            }

            // Check if we should use DKIM
            if (null !== ($dkim_settings = $configuration->get('dkim', null))) {
                $this->PHPMailer->DKIM_domain = $dkim_settings['domain'];
                $this->PHPMailer->DKIM_selector = $dkim_settings['selector'];
                $this->PHPMailer->DKIM_private = $dkim_settings['privatekey'];
                if (isset($dkim_settings['identity'])) {
                    $this->PHPMailer->DKIM_identity = $smtp_settings['identity'];
                }
                if (isset($dkim_settings['passphrase'])) {
                    $this->PHPMailer->DKIM_passphrase = $smtp_settings['passphrase'];
                }
            }

            // Allow custom settings
            if (null !== ($custom_settings = $configuration->get('custom', null))) {
                foreach ($custom_settings as $setting => $value) {
                    $this->PHPMailer->$setting = $value;
                }
            }

            // Set From
            $this->setFrom(
                    $configuration->get('core.from_address', 'noreply@example.org'), $configuration->get('core.from_name', $configuration->get('core.from_address', 'noreply@example.org'))
            );

            // Set Reply To
            if (null !== $configuration->get('core.replyto_address', null)) {
                $this->setReplyTo(
                        $configuration->get('core.replyto_address', 'noreply@example.org'), $configuration->get('core.replyto_name', $configuration->get('core.replyto_address', 'noreply@example.org'))
                );
            } else {
                $this->setReplyTo(
                        $configuration->get('core.from_address', 'noreply@example.org'), $configuration->get('core.from_name', $configuration->get('core.from_address', 'noreply@example.org'))
                );
            }
            
        } catch (\Exception $e) {
            $exception = new \KoolDevelop\Exception\Exception(__f('Error creating E-mail Message'));
            $exception->setDetail($e->__toString());
            throw new $exception;
        }
    }
    
    

    /**
     * Send E-mail
     * 
     * @return void
     */
    public function send() {

        if (count($this->To) == 0) {
            throw new \KoolDevelop\Exception\Exception(__f("No recipient for e-mail."));
        }

        if (empty($this->Subject)) {
            throw new \KoolDevelop\Exception\Exception(__f("No subject for e-mail."));
        }

        if (empty($this->Contents)) {
            throw new \KoolDevelop\Exception\Exception(__f("No contents for e-mail."));
        }


        try {

            $this->PHPMailer->AddReplyTo($this->ReplyTo, $this->ReplyToName);
            $this->PHPMailer->Sender = $this->From;
            $this->PHPMailer->SetFrom($this->From, $this->FromName);
            $this->PHPMailer->Subject = $this->Subject;
            foreach ($this->To as $recipient) {
                $this->PHPMailer->AddAddress($recipient[0], $recipient[1]);
            }
            foreach ($this->BCC as $bcc_recipient) {
                $this->PHPMailer->AddBCC($bcc_recipient[0], $bcc_recipient[1]);
            }
            foreach ($this->Attachments as $filename => $name) {
                $this->PHPMailer->AddAttachment($filename, $name);
            }
            $this->PHPMailer->MsgHTML($this->Contents);
            $this->PHPMailer->CharSet = 'utf-8';
            $this->PHPMailer->Encoding = 'base64';
            
            
            $this->PHPMailer->Send();
            
        } catch (\Exception $e) {
            $exception = new \KoolDevelop\Exception\Exception(__f('Error sending E-mail Message'));
            $exception->setDetail($e->__toString());
            throw new $exception;
        }
        
    }
    
    /**
     * Get list of (configurable) classes that this class
     * depends on. 
     * 
     * @return string[] Depends on
     */
    public static function getDependendClasses() {
        return array(
            '\\EmailView'
        );
    }
    
    /**
     * Get Configuration options for this class
     * 
     * @return \KoolDevelop\Configuration\IConfigurableOption[] Options for class
     */
    public static function getConfigurationOptions() {      
        return array(
            
            // From
            new \KoolDevelop\Configuration\IConfigurableOption('email', 'core.from_address', '"info@example.org"', ('Default from address')),
            new \KoolDevelop\Configuration\IConfigurableOption('email', 'core.from_name', '"Example"', ('Default from name')),
            
            // Reply to
            new \KoolDevelop\Configuration\IConfigurableOption('email', 'core.replyto_address', '"info@example.org"', ('Default from address'), false),
            new \KoolDevelop\Configuration\IConfigurableOption('email', 'core.replyto_name', '"Example"', ('Default from name'), false),

            // SMTP settings
            new \KoolDevelop\Configuration\IConfigurableOption('email', 'smtp.host', '', ('SMTP Hostname, comment smtp section to use default PHP mail() function'), false),
            new \KoolDevelop\Configuration\IConfigurableOption('email', 'smtp.username', '""', ('SMTP Username'), false),
            new \KoolDevelop\Configuration\IConfigurableOption('email', 'smtp.password', '""', ('SMTP Password'), false),
            
            // Custom
            new \KoolDevelop\Configuration\IConfigurableOption('email', 'custom.ConfirmReadingTo', '""', ('If you have custom PHPMailer settings add them to the custom section'), false),
            
        );
    }

}

?>
