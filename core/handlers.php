<?php

function main() {
    global $system, $system_user_id, $_user;
    if ($system->userinfo()['user_type'] < 1 || !$system->haveUserPermission($system_user_id, "ACCESS"))
        Location("/app/auth");
    $settings = $system->db()->query("SELECT * FROM `settings` LIMIT 1")->fetch_assoc();
    $content = '../core/template/dashboard.php';
    include '../core/template/default.php';
}

function auth() {
    global $system, $system_user_id, $_user;
    if ($system->auth() && $system->haveUserPermission($system_user_id, "ACCESS"))
        Location("/");
    $settings = $system->db()->query("SELECT * FROM `settings` LIMIT 1")->fetch_assoc();
    include '../core/template/auth/login.php';
}

function register() {
    global $system, $system_user_id, $_user;
    if ($system->auth() && $system->haveUserPermission($system_user_id, "ACCESS"))
        Location("/");
    $settings = $system->db()->query("SELECT * FROM `settings` LIMIT 1")->fetch_assoc();
    include '../core/template/auth/register.php';
}

function users() {
    global $system, $system_user_id, $_user;
    if($system->userinfo()['user_type'] < 2 || !$system->haveUserPermission($system_user_id, "MANAGE_USERS"))
        $system->printError(403);
    $content = '../core/template/manage-users/users.php';
    include '../core/template/default.php';
}

function users_add() {
    global $system, $system_user_id, $_user;
    if($system->userinfo()['user_type'] < 2 || !$system->haveUserPermission($system_user_id, "MANAGE_USERS"))
        $system->printError(403);
    $content = '../core/template/manage-users/users_add.php';
    include '../core/template/default.php';
}

function users_edit($args) {
    global $system, $system_user_id, $_user;
    if(!$system->haveUserPermission($system_user_id, "MANAGE_USERS"))
        $system->printError(403);
    $user_id = !empty(intval($args['id'])) ? intval($args['id']) : Location("/users");
    if (!$user = $system->userinfo($user_id))
        Location("/users");
    if ($user['user_type'] >= $system->userinfo()['user_type'])
        Location("/users");
    $content = '../core/template/manage-users/user.php';
    include '../core/template/default.php';
}

function users_delete($args) {
    global $system, $system_user_id, $_user;
    if(!$system->haveUserPermission($system_user_id, "MANAGE_USERS"))
        $system->printError(403);
    $user_id = !empty(intval($args['id'])) ? intval($args['id']) : Location("/users");
    if (!$user = $system->userinfo($user_id))
        Location("/users");
    if ($user['user_type'] >= $system->userinfo()['user_type'])
        Location("/users");
    $content = '../core/template/manage-users/user_delete.php';
    include '../core/template/default.php';
}

function settings() {
    global $system, $system_user_id, $_user;
    if(!$system->haveUserPermission($system_user_id, "MANAGE_SETTINGS_CMS"))
        $system->printError(403);
    $settings = $system->db()->query("SELECT * FROM `settings` LIMIT 1")->fetch_assoc();
    $content = '../core/template/settings/settings.php';
    include '../core/template/default.php';
}

function profile_password() {
    global $system, $system_user_id, $_user;
    if (!$system->haveUserPermission($system_user_id, "CHANGE_PASSWORD"))
        $system->printError(403);
    $content = '../core/template/edituser.php';
    include '../core/template/default.php';
}

function profile_avatar() {
    global $system, $system_user_id, $_user;
    if (!$system->haveUserPermission($system_user_id, "CHANGE_AVATAR"))
        $system->printError(403);
    $content = '../core/template/setavatar.php';
    include '../core/template/default.php';
}

function files_upload() {
    global $system, $system_user_id, $_user;
    if (!$system->haveUserPermission($system_user_id, "CREATE_FILES"))
        $system->printError(403);
    $content = '../core/template/files/upload.php';
    //include '../core/template/default.php';
}

function files_view($args) {
    global $system, $system_user_id, $_user;
    if (!$system->haveUserPermission($system_user_id, "VIEW_FILES"))
        $system->printError(403);
    $query = $system->db()->query('SELECT * FROM `files` WHERE `id` = "'.$args["id"].'"');
    if($query->num_rows == 0)
        $system->printError(404);
    $result = $query->fetch_assoc();
    print_r($result);
    $content = '../core/template/files/view.php';
    //include '../core/template/default.php';
}

function files_edit($args) {
    global $system, $system_user_id, $_user;
    if (!$system->haveUserPermission($system_user_id, "EDIT_FILES"))
        $system->printError(403);
    $content = '../core/template/files/edit.php';
    //include '../core/template/default.php';
}

function files_delete($args) {
    global $system, $system_user_id, $_user;
    if (!$system->haveUserPermission($system_user_id, "DELETE_FILES"))
        $system->printError(403);
    $content = '../core/template/files/delete.php';
    //include '../core/template/default.php';
}

// ================ API ================ \\

function api_login() {
    global $system, $system_user_id, $_user;
    if ($system->auth())
        res(3);

    $db = $system->db();
    $db->set_charset("utf8");
    $email = $db->real_escape_string($_REQUEST['email']);
    $password = $db->real_escape_string($_REQUEST['password']);
    $query = $db->query("SELECT * FROM `users` WHERE `email` = '$email'");
    if($query->num_rows == 0)
        res(0);
    $result = $query->fetch_assoc();
    if(!password_verify($password, $result['password']))
        res(0);
    if($result['email_verfied'] == 0 || $result['email_verfied'] == '0')
        res(102, $result['email_send_token']);

    $id = $result['id'];
    $solt = bin2hex(openssl_random_pseudo_bytes(20, $cstrong));
    if($id != 0 && !is_null($id)) {
        if($system->enabled_2fa($id)) {
            $user_code = $_REQUEST['auth_code'];
            if (is_null($user_code))
                res(100);
            if (!$system->auth_2fa($id, $user_code))
                res(101);
        }
        $query = $db->query("DELETE FROM `users_session` WHERE `id` = '$id' AND `usid` = '$solt'");
        $query = $db->query("INSERT INTO `users_session` (`id`, `usid`) VALUES ('$id', '$solt')");
        setcookie("id", $id, time()+(60*60*24*7), "/");
        setcookie("usid", $solt, time()+(60*60*24*7), "/");
    }

    res(1);
}

function api_register() {
    global $system;
    if ($system->auth()) {
        Location("/");
        res(0);
    }

    $db = $system->db();
    $db->set_charset("utf8");
    $email = $db->real_escape_string($_REQUEST['email']);
    $password = $db->real_escape_string($_REQUEST['password']);
    $password_repeat = $db->real_escape_string($_REQUEST['password_repeat']);
    $lastname = $db->real_escape_string($_REQUEST['lastname']);
    $surname = $db->real_escape_string($_REQUEST['surname']);
    $patronymic = $db->real_escape_string($_REQUEST['patronymic']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        res(2);
    if (($password != $password_repeat) || empty($password))
        res(3);
    if (mb_strlen($password) < 6)
        res(4);
    if (empty($lastname) || empty($surname))
        res(5);

    $query = $db->query("SELECT * FROM `users` WHERE `email` = '$email'");
    if ($query->num_rows !== 0)
        res(6);

    $passwordHash = $db->real_escape_string(password_hash($password, PASSWORD_DEFAULT));
    $emailVerifyHash = $db->real_escape_string(RandomString(20));
    $emailSendHash = $db->real_escape_string(RandomString(20));
    $time = $db->real_escape_string(time());
    if(!empty($patronymic))
        $query = $db->query("INSERT INTO `users` (`id`, `email`, `password`, `avatar`, `user_type`, `email_verfied`, `email_token`, `email_send_token`, `email_send_timestamp`, `2fa_secret`, `lastname`, `surname`, `patronymic`, `registred`) VALUES (NULL, '$email', '$passwordHash', '/assets/img/avatar.jpg', 1, 0, '$emailVerifyHash', '$emailSendHash', NULL, NULL, '$lastname', '$surname', '$patronymic', '$time')");
    else
        $query = $db->query("INSERT INTO `users` (`id`, `email`, `password`, `avatar`, `user_type`, `email_verfied`, `email_token`, `email_send_token`, `email_send_timestamp`, `2fa_secret`, `lastname`, `surname`, `patronymic`, `registred`) VALUES (NULL, '$email', '$passwordHash', '/assets/img/avatar.jpg', 1, 0, '$emailVerifyHash', '$emailSendHash', NULL, NULL, '$lastname', '$surname', NULL, '$time')");
    $query = $db->query("SELECT * FROM `users` WHERE `email` = '$email'");
    if ($query->num_rows !== 1)
        res(7);
    $system->send_email_verification($emailSendHash);
    res(1);
}

function api_email_resend($args) {
    global $system;
    $token = $args['token'];
    $link = $system->db()->query("SELECT * FROM `settings` LIMIT 1")->fetch_assoc()['link_to_admin'];
    $verification = $system->send_email_verification($token);
    /* switch($verification) {
        case 1:
            echo "Письмо успешно переотправлено. Если письмо не было доставлено, попробуйте через 5 минут или обратитесь к <a href='".$link."'>администратору</a>.<br><b>Не забудьте проверить папку спама!<b>";
            break;
        case 2:
            echo "Прежде чем попробовать снова, подождите 5 минут. Если после нескольких попыток переотправки письмо так и не приходит, обратитесь к <a href='".$link."'>администратору</a>. <br><b>Не забудьте проверить папку спама!<b>";
            break;
        default:
            echo "Произошла ошибка при отправке письма. Обратитесь к <a href='".$link."'>администратору</a>.";
            break;
    } */
    res($verification);
}

function api_email_verify($args) {
    global $system;
    $token = $args['token'];
    $link = $system->db()->query("SELECT * FROM `settings` LIMIT 1")->fetch_assoc()['link_to_admin'];
    $db = $system->db();
    $query = $db->query("SELECT * FROM `users` WHERE `email_token`='$token'");
    if($query->num_rows !== 1)
        exit("Токен не найден. Если считаете, что произошла ошибка, обратитесь к <a href='".$link."'>администратору<a>.");
    $db->query("UPDATE `users` SET `email_verfied` = '1' WHERE `users`.`email_token` = '$token';");
    $db->query("UPDATE `users` SET `email_send_token` = NULL WHERE `users`.`email_token`='$token'");
    $db->query("UPDATE `users` SET `email_token` = NULL WHERE `users`.`email_token`='$token'");
    exit("Ваш аккаунт успешно подтверждён! Теперь вы можете <a href='/app/auth'>авторизироваться</a>.");
}

function logout() {
    global $system, $system_user_id, $_user;
    if (!$system->auth())
        Location("/");
    $db = $system->db();
    $db->set_charset("utf8");
    $id = trim($_COOKIE['id']);
    $usid = trim($_COOKIE['usid']);
    $db->query("DELETE FROM `users_session` WHERE `id` = '$id' AND `usid` = '$usid'");
    setcookie("id", $id, time()-1, "/");
    setcookie("usid", $solt, time()-1, "/");
    Location("/");
}

function api_users_add() {
    global $system, $system_user_id, $_user;
    if(!$system->haveUserPermission($system_user_id, "MANAGE_USERS"))
        res(0, "Ошибка доступа");
    $user_role = $system->userinfo()['user_type'];
    $role = !empty(intval($_POST['role'])) ? intval($_POST['role']) : $role = 1;
    if ($role < 1)
        res(0, "Роль не может равняться нулю или быть меньше него");
    if ($user_role <= $role)
        res(0, "Ваша роль меньше или равна создаваемой");
    if(empty($_POST['login']) || empty($_POST['password']))
        res(0, "Заполните все поля");
    $db = $system->db();
    $db->set_charset("utf8");
    $login = $db->real_escape_string(trim($_POST['login']));
    $query = $db->query("SELECT * FROM `users` WHERE `login` = '$login' LIMIT 1");
    if ($query->num_rows !== 0)
        res(0, "Пользователь с таким логином уже существует");
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $db->query("INSERT INTO `users`(`login`, `password`, `avatar`, `user_type`, `coins`, `minutes`, `afk_minutes`, `work_minutes`, `work_afk_minutes`) VALUES ('$login', '$password', '/assets/img/profile.jpg', '$role', '0', '0', '0', '0', '0')");
    $query = $db->query("SELECT * FROM `users` WHERE `login` = '$login' LIMIT 1");
    $result = $query->fetch_assoc();
    $id = $result['id'];
    $db->query("INSERT INTO `permissions` (`id`, `userid`, `DASHBOARD`, `VIEWING_HISTORY_OF_PUNISHMENTS`, `CHANGE_AVATAR`, `CHANGE_PASSWORD`, `DOWNLOAD_MODERTOOL`, `VIEWING_LOGS`, `ACCESS_TO_LOGS`, `CHAT_LOGS`, `CMDS_LOGS`, `JOINS_LOGS`, `KILLS_LOGS`, `MANAGE_SETTINGS_CMS`, `CONTROL_IMAGE_HOST_TOKENS`, `MANAGE_USERS`, `VIEWING_PROMOCODES`, `ADDING_PROMOCODES`, `MANAGE_PROMOCODES`, `VIEWING_LIST_PLAYERS`, `VIEWING_LIST_PLAYERS_IP`, `PM_TO_PLAYER`, `KICK_PLAYER`, `MUTE_PLAYER`, `UNMUTE_PLAYER`, `WAKE_UP_HELPERS`, `MANAGE_COINS_USERS`, `VIEWING_ONLINE_TABLE`, `RCON_SURVIVAL`, `WORK_ACCOUNTS_MOD`, `WORK_ACCOUNTS_OTHER`) VALUES (NULL, '$id', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0');");
    res(1, "Пользователь " . $login . " успешно создан в базе");
}

function api_users_edit() {
    global $system, $system_user_id, $_user;
    if(!$system->haveUserPermission($system_user_id, "MANAGE_USERS"))
        res(0, "Ошибка доступа");
    $user_id = !empty(intval($_POST['id'])) ? intval($_POST['id']) : res(0, "Ошибка доступа");
    if (!$user = $system->userinfo($user_id))
        res(0, "Ошибка");
    if(!is_numeric($_POST['role']))
        res(4, "Выберите роль и попробуйте снова");
    $role = !empty(intval($_POST['role'])) || $_POST['role'] < 1 ? intval($_POST['role']) : 1;
    $user_role = $system->userinfo()['user_type'];
    if ($user_role <= $user['user_type'] || $role >= $user_role)
        res(0, "Ваша роль ниже чем у данного пользователя");
    $db = $system->db();
    $db->set_charset("utf8");
    $login = strlen($_POST['login']) > 3 ? $db->real_escape_string($_POST['login']) : res("Логин не может быть короче 3 символов");
    $password = $db->real_escape_string(trim($_POST['password']));
    if (empty($password))
        $db->query("UPDATE `users` SET `login` = '$login', `user_type` = '$role' WHERE `id` = '$user_id'");
    else {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $db->query("UPDATE `users` SET `login` = '$login', `password` = '$password', `user_type` = '$role' WHERE `id` = '$user_id'");
    }
    res(1, "Данные пользователя успешно обновлены");
}

function api_users_delete() {
    global $system, $system_user_id, $_user;
    if(!$system->haveUserPermission($system_user_id, "MANAGE_USERS"))
        res(0, "Ошибка доступа");
    $id = intval($_POST['id']) > 0 ? intval($_POST['id']) : res(0, 'Выберите пользователя');
    $user_role = $system->userinfo()['user_type'];
    $login = $system->userinfo($id)['login'];
    if ($user_role <= $system->userinfo($id)['user_type'])
        res(0, "Ваша роль меньше или равна удаляемому пользователю");
    $system->db()->query("INSERT INTO `users_deleted` SELECT * FROM `users` WHERE `id` = '$id'");
    $system->db()->query("DELETE FROM `users` WHERE `id` = '$id'");
    $system->db()->query("DELETE FROM `users_session` WHERE `id` = '$id'");
    res(1, "Пользователь ". $login . " успешно удален");
}

function api_user_permissions() {
    global $system, $system_user_id, $_user;
    if(!$system->haveUserPermission($system_user_id, "MANAGE_USERS"))
        Location("/");
    $db = $system->db();
    header("Content-Type: application/json");
    $data = json_decode(file_get_contents("php://input"));
    $user_id = $data[0][1];
    for($i = 0; $i < sizeof($data); $i++) {
        if($data[$i][0] == "id") continue;
        if($data[$i][1] && $system->haveUserPermission($_user['id'], $data[$i][0])) {
            //echo '<b>True</b> ' . $data[$i][0] . "<br>";
            $db->query("UPDATE `permissions` SET ".$data[$i][0]." = 1 WHERE `userid` = $user_id");
        }
        else if(!$data[$i][1] && $system->haveUserPermission($_user['id'], $data[$i][0])) {
            //echo 'False ' . $data[$i][0] . "<br>";
            $db->query("UPDATE `permissions` SET ".$data[$i][0]." = 0 WHERE `userid` = $user_id");
        }
    }
    res(1, "Права успешно обновлены!");
}

function api_settings_update() {
    global $system, $system_user_id, $_user;
    if (!$system->haveUserPermission($system_user_id, "MANAGE_SETTINGS_CMS"))
        res(0, "Ошибка доступа");
    $days = intval($_POST['days']) > 0 ? intval($_POST['days']) : res(0, 'Укажите целое положительное число (days)');
    $logs_in_page = intval($_POST['logs_in_page']) > 0 ? intval($_POST['logs_in_page']) : res(0, 'Укажите целое положительное число (logs_in_page)');
    $max_size_avatar = intval($_POST['max_size_avatar']) > 0 ? intval($_POST['max_size_avatar']) : res(0, 'Укажите целое положительное число (max_size_avatar)');
    $link_to_admin = empty($_POST['link_to_admin']) ? res(0, "Укажите ссылку на администратора") : $_POST['link_to_admin'];
    $system->db()->query("UPDATE `settings` SET `delete_after` = '$days', `logs_in_page` = '$logs_in_page', `max_size_avatar` = '$max_size_avatar', `link_to_admin` = '$link_to_admin' WHERE 1");
    res(1, "Настройки успешно обновлены");
}

function api_user_changepassword() {
    global $system, $system_user_id, $_user;
    if (!$system->auth()) //(!$system->haveUserPermission($system_user_id, "CHANGE_PASSWORD"))
        Location("/");
    if(empty($_REQUEST['password'])) { echo 123; return; };

    $user_id = $_user['id'];
    $db = $system->db();
    $db->set_charset("utf8");
    $password = $db->real_escape_string(trim($_REQUEST['password']));
    $password = password_hash($password, PASSWORD_DEFAULT);
    $db->query("UPDATE `users` SET `password` = '$password' WHERE `id` = '$user_id'");
    $id = trim($_COOKIE['id']);
    $db->query("DELETE FROM `users_session` WHERE `id` = '$id'");
    setcookie("id", $id, time()-1, "/");
    setcookie("usid", $solt, time()-1, "/");
    res(1, "Пароль успешно изменен! Переавторизируйтесь на сайте, текущая сессия закрыта.");
}

function api_user_setavatar() {
    global $system, $system_user_id, $_user;
    if (!$system->haveUserPermission($system_user_id, "CHANGE_AVATAR"))
        Location("/");
    $settings = $system->db()->query("SELECT * FROM `settings` LIMIT 1")->fetch_assoc();
    $user_id = $_user['id'];
    if($_FILES['avatar']['tmp_name']) {
        if($_FILES['avatar']['type'] != 'image/jpeg') {
            //res(0, "Неверный тип изображения.");
            echo "Неверный тип изображения.";
            return;
        }
        if($_FILES['avatar']['size'] >= $settings['max_size_avatar'] * MB) {
            //res(0, "Вес не должен превышать 2 МБ.");
            echo "Вес не должен превышать ". $settings['max_size_avatar'] ." МБ.";
            return;
        }
    }
    $db = $system->db();
    $db->set_charset("utf8");

    $image = imagecreatefromjpeg($_FILES['avatar']['tmp_name']);
    $permitted_char = '0123456789ABCDEFHKLMNOPRSTUYabcdefhklmnoprstuy-_';
    $filename = substr(str_shuffle($permitted_char), 0, 11);

    if(mkdir('user-avatars/' . $user_id . '/', 0777));
    $dir = 'user-avatars/' . $user_id . '/' . $filename;

    imagejpeg($image, $dir . '.jpg');
    $db->query("UPDATE `users` SET `avatar` = '/$dir.jpg' WHERE `id` = '$user_id'");
    imagedestroy($tmp);
    //res(1, "Аватарка изменена.");
    echo "Аватарка изменена.";
    Location("/");
}

function download_moderation_tool() {
    global $system, $system_user_id, $_user;
    if (!$system->haveUserPermission($system_user_id, "DOWNLOAD_MODERTOOL"))
        Location("/");
    $file = "../public_html/moderation-tool/Mc5zsXr/Moderation Tool Installer.exe";
    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($file).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }
}