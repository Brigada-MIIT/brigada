<?php
declare(strict_types=1);
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class System {
    function db() {
        return new mysqli(db_host, db_user, db_password, db_basename);
    }
    
    function remote_db($host, $user, $password, $basename) {
        return new mysqli($host, $user, $password, $basename);
    }

    function auth() {
        if (!isset($_COOKIE['id']) || !isset($_COOKIE['usid'])){
            return false;
        }
        $id = trim($_COOKIE['id']);
        $usid = trim($_COOKIE['usid']);
        $db = $this->db();
        $query = $db->query("SELECT * FROM `users_session` WHERE `id` = '$id' AND `usid` = '$usid'");
        $query2 = $db->query("SELECT * FROM `users` WHERE `id` = '$id'");
        $result = $query->fetch_assoc();
        $result2 = $query2->fetch_assoc();
        $usid_f = $result['usid'];
        if($usid !== $usid_f) {
            return false;
        }
        if($query->num_rows == 1 && $query2->num_rows == 0) {
            $db->query("DELETE FROM `users_session` WHERE `id`=". $id .";");
        }
        if ($query->num_rows == 1 && $query2->num_rows == 1)
            return true;
        else
            return false;
    }

    function userinfo($id = false) {
        $db = $this->db();
        if ($id == false) {
            if(isset($_COOKIE['id']))
                $id = trim($_COOKIE['id']);
            else return false;
        }
        $query = $db->query("SELECT * FROM `users` WHERE `id` = '$id'");
        return $query->num_rows == 1 ? $query->fetch_assoc() : false;
    }

    function haveGroupPermissions($id, $permission) {
        if(!$id || !$permission) return false;
        $db = $this->db();
        $query = $db->query("SELECT `".$permission."` FROM `groups` WHERE `id`='".$id."';");
        $result = $query->fetch_assoc();
        return $result[$permission];
    }

    function haveUserGroupPermissions($id, $permission) {
        if(!$id || !$permission) return false;
        $id = $this->userinfo($id)['user_type'];
        $db = $this->db();
        $query = $db->query("SELECT `".$permission."` FROM `groups` WHERE `id`='".$id."';");
        $result = $query->fetch_assoc();
        return $result[$permission];
    }

    function haveUserApartPermission($id, $permission) { // отдельные разрешения
        if(!$id || !$permission) return false;
        $db = $this->db();
        $query = $db->query("SELECT `".$permission."` FROM `permissions` WHERE `userid`='".$id."';");
        $result = $query->fetch_assoc();
        if(!$query->num_rows)
            return false;
        return $result[$permission];
    }

    function haveUserPermission($id, $permission) {
        if(!$this->auth()) return false;
        if(!$id || !$permission) return false;
        if($this->userinfo($id)['ban'] != 0)
            $this->printError(100);
        if($this->haveGroupPermissions($this->userinfo($id)['user_type'], $permission) || $this->haveUserApartPermission($id, $permission))
            return true;
        else
            return 0;
    }

    function haveUserPermissionToAuth($id) {
        if(!$id) return false;
        if($this->haveGroupPermissions($this->userinfo($id)['user_type'], "DASHBOARD") || $this->haveUserApartPermission($id, "DASHBOARD"))
            return true;
        else
            return 0;
    }

    function getNameRole($id) {
        if(!$id) return false;
        $db = $this->db();
        $query = $db->query("SELECT * FROM `groups` WHERE `id`='$id'");
        $result = $query->fetch_assoc();
        return $result['name'];
    }
    function printError($error) {
        include __DIR__ . "/template/errors/" . $error . '.php';
        if($error == 403) {
            if(!empty($_COOKIE['last'])) {
                $location = trim($_COOKIE['last']);
                setcookie("last", $location, time()-1, "/");
            }
        }
        die();
    }

    function enabled_2fa($id) {
        if(!$id) return false;
        $db = $this->db();
        $secret = $db->query("SELECT `2fa_secret` FROM `users` WHERE `id`='$id'")->fetch_assoc()['2fa_secret'];
        if(is_null($secret))
            return false;
        return true;
    }

    function auth_2fa($id, $user_code) {
        if(!$id) return false;
        $db = $this->db();
        $secret = $db->query("SELECT `2fa_secret` FROM `users` WHERE `id`='$id'")->fetch_assoc()['2fa_secret'];
        if(!$this->enabled_2fa($id))
            return 1;
        $auth = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();
        $code = $auth->getCode($secret);
        if((int)$user_code == (int)$code)
            return 1;
        return 0;
    }

    function send_email_verification($token) {
        if (!$token) return false;
        $db = $this->db();
        $query = $db->query("SELECT * FROM `users` WHERE `email_send_token`='$token'");
        if($query->num_rows !== 1)
            return 0;
        $result = $query->fetch_assoc();
        $time = time();

        /* Генерация письма */
        $mail = new PHPMailer;
        $mail->setFrom('noreply@brigada-miit.ru', 'Файлообменник «Бригада»');
        $mail->addAddress($result['email'], '');
        $mail->CharSet = 'UTF-8';
        $mail->Subject ='Файлообменник «Бригада». Подтверждение регистрации';
        $mail->IsHTML(true);
        $mail->msgHTML('
            <!DOCTYPE html>
            <html lang="ru">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        line-height: 1.6;
                        margin: 0;
                        padding: 0;
                    }
            
                    .container {
                        max-width: 600px;
                        padding: 20px;
                        background-color: #fff;
                        border-radius: 15px;
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    }
            
                    .btn {
                        display: inline-block;
                        background-color: #007bff;
                        color: #fff;
                        text-decoration: none;
                        padding: 10px 20px;
                        border-radius: 5px;
                    }
            
                    .btn:hover {
                        background-color: #0056b3;
                    }
            
                    .message {
                        margin-bottom: 20px;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="message">
                        <p>Добро пожаловать, ' . $result["surname"] . '! Мы рады вас приветствовать на нашем файлообменнике!</p>
                        <p>Прежде чем начать пользоваться файлообменником, пожалуйста, подтвердите аккаунт нажатием по кнопке:</p>
                        <p><a class="btn" href="https://brigada-miit.ru/email/verify/' . $result["email_token"] . '">Подтвердить аккаунт</a></p>
                        <p><b>ВНИМАНИЕ! Если вы не регистрировались на нашем сервисе, пожалуйста, проигнорируйте это письмо и НЕ ПЕРЕХОДИТЕ ПО ССЫЛКЕ ПОДТВЕРЖДЕНИЯ!</b></p>
                        <p>С уважением, администрация файлообменника «Бригада» <a href="https://brigada-miit.ru">brigada-miit.ru</a></p>
                    </div>
                </div>
            </body>
            </html>
        ');

        $mail->DKIM_domain = 'brigada-miit.ru';
        $mail->DKIM_private = 'vendor/dkim_private.pem';
        $mail->DKIM_selector = 'mail';
        $mail->DKIM_identity = $mail->From;
        /*******************/

        if(!isset($result['email_send_timestamp'])) {
            if(!$mail->send()) return 0;
            $query = $db->query("UPDATE `users` SET `email_send_timestamp` = '$time' WHERE `users`.`email_send_token` = '$token';");
            return 1;
        }
        else if((time() - intval($result['email_send_timestamp'])) > 300) {
            if(!$mail->send()) return 0;
            $query = $db->query("UPDATE `users` SET `email_send_timestamp` = '$time' WHERE `users`.`email_send_token` = '$token';");
            return 1;
        }
        else return 2; // если не прошло 5 минут с момента последней отправки
    }

    function send_email_change_password($_user) {
        if(!empty($_user['password_send_timestamp'])) {
            if((time() - intval($_user['password_send_timestamp'])) < 300)
                return 2; // если не прошло 5 минут с момента последней отправки
        }
        $db = $this->db();
        $user_id = $_user['id'];
        $password_token = $db->real_escape_string(RandomString(20));
        $time = time();

        /* Генерация письма */
        $mail = new PHPMailer;
        $mail->setFrom('noreply@brigada-miit.ru', 'Файлообменник «Бригада»');
        $mail->addAddress($_user['email'], '');
        $mail->CharSet = 'UTF-8';
        $mail->Subject ='Файлообменник «Бригада». Смена пароля';
        $mail->IsHTML(true);
        $mail->msgHTML('
            <!DOCTYPE html>
            <html lang="ru">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        line-height: 1.6;
                        margin: 0;
                        padding: 0;
                    }
            
                    .container {
                        max-width: 600px;
                        padding: 20px;
                        background-color: #fff;
                        border-radius: 15px;
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    }
            
                    .btn {
                        display: inline-block;
                        background-color: #007bff;
                        color: #fff;
                        text-decoration: none;
                        padding: 10px 20px;
                        border-radius: 5px;
                    }
            
                    .btn:hover {
                        background-color: #0056b3;
                    }
            
                    .message {
                        margin-bottom: 20px;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="message">
                        <p>Здравствуйте, '. $_user["surname"] . '! Для вашего аккаунта была запрошена смена пароля.</p>
                        <p>Время действия ссылки смены пароля составляет <strong>1 СУТКИ</strong>. Чтобы изменить пароль, пожалуйста, нажмите на кнопку ниже:</p>
                        <p><a class="btn" href="https://brigada-miit.ru/password/change/' . $password_token . '">Изменить пароль</a></p>
                        <p><b>ВНИМАНИЕ! Если вы не запрашивали смену пароля на нашем сервисе, пожалуйста, проигнорируйте это письмо и НЕ ПЕРЕХОДИТЕ ПО ССЫЛКЕ ИЗМЕНЕНИЯ ПАРОЛЯ!</b></p>
                        <p>С уважением, администрация файлообменника «Бригада» <a href="https://brigada-miit.ru">brigada-miit.ru</a></p>
                    </div>
                </div>
            </body>
            </html>
        ');

        $mail->DKIM_domain = 'brigada-miit.ru';
        $mail->DKIM_private = '../core/vendor/dkim_private.pem';
        $mail->DKIM_selector = 'mail';
        $mail->DKIM_identity = $mail->From;
        /*******************/

        if(!$mail->send()) return 0;
        $query = $db->query("UPDATE `users` SET `password_send_timestamp` = '$time', `password_token` = '$password_token' WHERE `users`.`id` = '$user_id'");
        if(!$query) return -1;
        return 1;
    }
}

function res($code, $text = false) {
    if ($text)
        exit(json_encode(["result" => $code, "text" => $text]));
    else
        exit(json_encode(["result" => $code]));
}

function Location($location = "/", $last = false) {
    if($last) {
        setcookie("last", $last, time()+(60*60*24*7), "/");
    }
    header('Location: ' . $location);
    exit();
}

function RandomString($length) {
    $keys = array_merge(
         range(0,9), 
         array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z')
    );

    $key = '';

    for($i=0; $i < $length; $i++) {
        $key .= $keys[mt_rand(0, count($keys) - 1)];
    }

    return $key;
}

function unixDateToString($timestamp) {
    $day = gmdate('j', $timestamp);
    $month = gmdate('n', $timestamp);
    $year = gmdate('Y', $timestamp);

    $months = array(
        1 => 'января',
        2 => 'февраля',
        3 => 'марта',
        4 => 'апреля',
        5 => 'мая',
        6 => 'июня',
        7 => 'июля',
        8 => 'августа',
        9 => 'сентября',
        10 => 'октября',
        11 => 'ноября',
        12 => 'декабря'
    );

    $result = $day . " " . $months[$month] . " " . $year;

    return $result;
}    

function countWhiteSpaces($s) {
    if(is_numeric($s)) return 0;
    return substr_count($s, ' ');
}

function fileIconName($name) {
    // 'jpg', 'jpeg', 'gif', 'png', 'docx', 'doc', 'txt', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'zip'
    switch($name) {
        case 'jpg':
            return "image";
            break;
        case 'jpeg':
            return "image";
            break;
        case 'gif':
            return "image";
            break;
        case 'png':
            return "image";
            break;
        case 'docx':
            return "word";
            break;
        case 'doc':
            return "word";
            break;
        case 'txt':
            return "txt";
            break;
        case 'xls':
            return "excel";
            break;
        case 'xlsx':
            return "excel";
            break;
        case 'ppt':
            return "powerpoint";
            break;
        case 'pptx':
            return "powerpoint";
            break;
        case 'pdf':
            return "pdf";
            break;
        case 'zip':
            return "zip";
            break;
        default:
            return "file";
            break;
    }
}

function formatFileSize($size) {
    $units = array('Б', 'кБ', 'МБ', 'ГБ', 'ТБ');

    for ($i = 0; $size > 1024; $i++) {
        $size /= 1024;
    }

    return round($size, 1) . ' ' . $units[$i];
}
