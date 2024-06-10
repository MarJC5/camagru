<?php 

namespace Camagru\mail;

use Camagru\helpers\Env;
use function Camagru\mail_path;

class Mail {
    /**
     * Constants for mail charset and content type
     */
    const CHARSET_ASCII = 'us-ascii';
    const CHARSET_ISO88591 = 'iso-8859-1';
    const CHARSET_UTF8 = 'utf-8';

    /**
     * Constants for mail content type
     */
    const CONTENT_TYPE_PLAINTEXT = 'text/plain';
    const CONTENT_TYPE_TEXT_HTML = 'text/html';

    /**
     * Config
     */
    public $sendmail_path;
    public $host;
    public $port;
    public $sendmail_from;
    public $name;
    public $from;

    public function __construct() {
        $this->sendmail_path = Env::get('MAIL_SENDMAIL_PATH');
        $this->host = Env::get('MAIL_HOST');
        $this->port = Env::get('MAIL_PORT');
        $this->sendmail_from = Env::get('MAIL_FROM_ADDRESS');
        $this->name = Env::get('MAIL_FROM_NAME');
        $this->from = "{$this->name} <{$this->sendmail_from}>";
    }

    public static function send($to, $subject, $template, $data, $charset = self::CHARSET_UTF8, $contentType = self::CONTENT_TYPE_TEXT_HTML) {
        $mail = new Mail();

        $headers = "From: {$mail->from}\r\n";
        $headers .= "Reply-To: {$mail->sendmail_from}\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: {$contentType} ;charset={$charset}" . "\r\n";

        $content = $mail->template($template, $data);

        if (!mail($to, $subject, $content, $headers)) {
            return false;
        }

        return true;
    }

    protected function template($template, $data) {
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

    public static function sendmail($to, $subject, $message, $headers) {
        return mail($to, $subject, $message, $headers);
    }

    public function debug() {
        return [
            'sendmail_path' => $this->sendmail_path,
            'host' => $this->host,
            'port' => $this->port,
            'sendmail_from' => $this->sendmail_from,
            'name' => $this->name,
            'from' => $this->from
        ];
    }

    public function setSendmailPath($sendmail_path) {
        $this->sendmail_path = $sendmail_path;
    }

    public function setHost($host) {
        $this->host = $host;
    }

    public function setPort($port) {
        $this->port = $port;
    }

    public function setSendmailFrom($sendmail_from) {
        $this->sendmail_from = $sendmail_from;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setFrom($from) {
        $this->from = $from;
    }
}