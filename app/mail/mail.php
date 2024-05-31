<?php 

namespace Camagru\mail;

use Camagru\helpers\Env;
use function Camagru\mail_path;

class Mail {
    public static function send($to, $subject, $template, $data) {
        $headers = 'From: <' . Env::get('MAIL_FROM_ADDRESS') . ">\r\n";
        $headers .= 'Reply-To: ' . Env::get('MAIL_FROM_ADDRESS') . "\r\n";
        $headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";

        if (!mail($to, $subject, self::template($template, $data), $headers)) {
            return false;
        }

        return true;
    }

    public static function template($template, $data) {
        $template = mail_path('/templates/' . $template . '.html');
        if (!file_exists($template)) {
            return false;
        }
        $template = file_get_contents($template);
        foreach ($data as $key => $value) {
            $template = str_replace('{{' . $key . '}}', htmlspecialchars($value, ENT_QUOTES, 'UTF-8'), $template);
        }
    
        return $template;

        return $template;
    }
}