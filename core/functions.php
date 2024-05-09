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
        /*$solt = md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['HTTP_ACCEPT_LANGUAGE']);
        if ($usid !== $solt){
            return false;
        }
        $query = $db->query("SELECT * FROM `users_session` WHERE `id` = '$id' AND `usid` = '$solt'");*/
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
            Добро пожаловать, ' . $result["surname"] . '! Мы ради вас приветствовать на нашем файлообменнике!<br>
            Прежде чем начать пользоваться файлообменником, пожалуйста, подтвердите аккаунт.<br><br>
            <strong><a href="https://brigada-miit.ru/email/verify/' . $result["email_token"] . '">ПОДТВЕРДИТЬ АККАУНТ</a></strong><br><br>
            <strong>ВНИМАНИЕ! Если вы не регистрировались на нашем сервисе, пожалуйста, проигнорируйте это письмо и НЕ ПЕРЕХОДИТЕ ПО ССЫЛКЕ ПОДТВЕРЖДЕНИЯ!</strong><br><br>
            С уважением, администрация файлообменника «Бригада» <a href="https://brigada-miit.ru">brigada-miit.ru</a>
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
}

function res($code, $text = false) {
    if ($text)
        exit(json_encode(["result" => $code, "text" => $text]));
    else
        exit(json_encode(["result" => $code]));
}

function Location($location) {
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
        // 'txt', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'zip'
        default:
            return "file";
            break;
    }
}