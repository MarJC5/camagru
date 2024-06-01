<?php 

namespace Camagru\mail;

use Camagru\helpers\Env;
use function Camagru\mail_path;

class Mail {
    public static function send($to, $subject, $template, $data) {
        $displayName = Env::get('MAIL_FROM_NAME');
        $mailFrom = Env::get('MAIL_FROM_ADDRESS');
        
        $headers = "From: {$displayName} <{$mailFrom}>\r\n";
        $headers .= "Reply-To: {$mailFrom}\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html;charset=UTF-8" . "\r\n";

        $content = self::template($template, $data);

        if (!mail($to, $subject, $content, $headers)) {
            return false;
        }

        return true;
    }

    public static function template($template, $data) {
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
}