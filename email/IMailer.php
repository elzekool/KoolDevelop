<?php
/**
 * Interface for Mailer
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Email
 **/

namespace KoolDevelop\Email;

/**
 * Interface for Mailer
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
interface IMailer
{

    /**
     * Set From address
     *
     * @param string $address Address
     * @param string $name    Name
     *
     * @return void
     */
    public function setFrom($address, $name);

    /**
     * Set Reply-To
     *
     * @param string $address Address
     * @param string $name    Name
     *
     * @return void
     */
    public function setReplyTo($address, $name);

    /**
     * Add Blind Carbon Copy Recipient
     *
     * @param string $address Address
     * @param string $name    Name
     *
     * @return void
     */
    public function addBCC($address, $name);

    /**
     * Add Recipient
     *
     * @param string $address Address
     * @param string $name    Name
     *
     * @return void
     */
    public function addTo($address, $name);

    /**
     * Add attachment
     *
     * @param string $filename Filename (full path)
     * @param string $name     Name
     *
     * @return \KoolDevelop\Email\Message Self
     */
    public function addAttachment($filename, $name = null);

    /**
     * Set Subject
     *
     * @param string $subject Subject
     *
     * @return void
     */
    public function setSubject($subject);

    /**
     * Set Contents of E-mail Message
     *
     * @param string $content Contents
     *
     * @return void
     */
    public function setContents($content);

    /**
     * Reset settings, allowing to send new message
     *
     * @return void
     */
    public function reset();

    /**
     * Send E-mail
     *
     * @return void
     */
    public function send();

}