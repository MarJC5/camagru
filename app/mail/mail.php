<?php

namespace Camagru\mail;

use Camagru\helpers\Env;
use function Camagru\mail_path;

/**
 * Class Mail
 * Handles sending emails using specified templates and configurations.
 */
class Mail
{
    // Charset constants
    const CHARSET_ASCII = 'us-ascii';
    const CHARSET_ISO88591 = 'iso-8859-1';
    const CHARSET_UTF8 = 'utf-8';

    // Content type constants
    const CONTENT_TYPE_PLAINTEXT = 'text/plain';
    const CONTENT_TYPE_TEXT_HTML = 'text/html';

    // Mail configuration properties
    public $sendmail_path;
    public $host;
    public $port;
    public $sendmail_from;
    public $name;
    public $from;

    /**
     * Mail constructor.
     * Loads email configurations from environment variables.
     */
    public function __construct()
    {
        $this->sendmail_path = Env::get('MAIL_SENDMAIL_PATH');
        $this->host = Env::get('MAIL_HOST');
        $this->port = Env::get('MAIL_PORT');
        $this->sendmail_from = Env::get('MAIL_FROM_ADDRESS');
        $this->name = Env::get('MAIL_FROM_NAME');
        $this->from = "{$this->name} <{$this->sendmail_from}>";
    }

    /**
     * Send an email using a template.
     *
     * @param string $to The recipient email address.
     * @param string $subject The subject of the email.
     * @param string $template The name of the email template.
     * @param array $data The data to populate the template.
     * @param string $charset The charset to use for the email.
     * @param string $contentType The content type of the email.
     * @return bool True if the email was sent successfully, false otherwise.
     */
    public static function send($to, $subject, $template, $data, $charset = self::CHARSET_UTF8, $contentType = self::CONTENT_TYPE_TEXT_HTML)
    {
        $mail = new Mail();

        $headers = "From: {$mail->from}\r\n";
        $headers .= "Reply-To: {$mail->sendmail_from}\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: {$contentType}; charset={$charset}\r\n";

        $content = $mail->template($template, $data);

        return mail($to, $subject, $content, $headers);
    }

    /**
     * Load and populate an email template with data.
     *
     * @param string $template The name of the template.
     * @param array $data The data to populate the template.
     * @return string|false The populated template content, or false if the template file does not exist.
     */
    protected function template($template, $data)
    {
        $templatePath = mail_path('/templates/' . $template . '.php');
        if (!file_exists($templatePath)) {
            return false;
        }

        $templateContent = file_get_contents($templatePath);
        foreach ($data as $key => $value) {
            $templateContent = str_replace('{{' . $key . '}}', htmlspecialchars($value, ENT_QUOTES, 'UTF-8'), $templateContent);
        }

        return $templateContent;
    }

    /**
     * Send a raw email message.
     *
     * @param string $to The recipient email address.
     * @param string $subject The subject of the email.
     * @param string $message The email message.
     * @param string $headers The email headers.
     * @return bool True if the email was sent successfully, false otherwise.
     */
    public static function sendmail($to, $subject, $message, $headers)
    {
        return mail($to, $subject, $message, $headers);
    }

    /**
     * Get the current mail configuration for debugging purposes.
     *
     * @return array An array containing the current mail configuration.
     */
    public function debug()
    {
        return [
            'sendmail_path' => $this->sendmail_path,
            'host' => $this->host,
            'port' => $this->port,
            'sendmail_from' => $this->sendmail_from,
            'name' => $this->name,
            'from' => $this->from
        ];
    }

    /**
     * Set the sendmail path.
     *
     * @param string $sendmail_path The sendmail path.
     */
    public function setSendmailPath($sendmail_path)
    {
        $this->sendmail_path = $sendmail_path;
    }

    /**
     * Set the mail server host.
     *
     * @param string $host The mail server host.
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * Set the mail server port.
     *
     * @param int $port The mail server port.
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * Set the sender email address.
     *
     * @param string $sendmail_from The sender email address.
     */
    public function setSendmailFrom($sendmail_from)
    {
        $this->sendmail_from = $sendmail_from;
    }

    /**
     * Set the sender name.
     *
     * @param string $name The sender name.
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Set the sender information.
     *
     * @param string $from The sender information.
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }
}
