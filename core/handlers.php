<?php

function main() {
    global $system, $system_user_id, $_user;
    /*if ($system->userinfo()['user_type'] < 1 || !$system->haveUserPermission($system_user_id, "ACCESS"))
        Location("/app/auth");*/    
    if($system->auth() && $_user['ban'] != 0)
        $system->printError(100);
    $title = "Бригада | Главная";
    $settings = $system->db()->query("SELECT * FROM `settings` LIMIT 1")->fetch_assoc();
    $content = '../core/template/main.php';
    include '../core/template/default.php';
}

function auth() {
    global $system, $system_user_id, $_user;
    if ($system->auth() && $system->haveUserPermission($system_user_id, "ACCESS"))
        Location("/");
    $title = "Бригада | Авторизация";
    $settings = $system->db()->query("SELECT * FROM `settings` LIMIT 1")->fetch_assoc();
    $content = '../core/template/auth/login.php';
    include '../core/template/default.php';
}

function register() {
    global $system, $system_user_id, $_user;
    if ($system->auth() && $system->haveUserPermission($system_user_id, "ACCESS"))
        Location("/");
    $title = "Бригада | Регистрация";
    $settings = $system->db()->query("SELECT * FROM `settings` LIMIT 1")->fetch_assoc();
    $content = '../core/template/auth/register.php';
    include '../core/template/default.php';
}

function users() {
    global $system, $system_user_id, $_user;
    if(!$system->haveUserPermission($system_user_id, "MANAGE_USERS"))
        $system->printError(403);
    $content = '../core/template/users/users.php';
    include '../core/template/default.php';
}

function users_edit($args) {
    global $system, $system_user_id, $_user;
    if(!$system->haveUserPermission($system_user_id, "MANAGE_USERS"))
        $system->printError(403);
    $user_id = !empty(intval($args['id'])) ? intval($args['id']) : Location("/users");
    if (!$user = $system->userinfo($user_id))
        Location("/app/users");
    if ($user['user_type'] >= $system->userinfo()['user_type'])
        Location("/app/users");
    $settings = $system->db()->query("SELECT * FROM `settings` LIMIT 1")->fetch_assoc();
    $content = '../core/template/users/edit.php';
    include '../core/template/default.php';
}

function settings() {
    global $system, $system_user_id, $_user;
    if(!$system->haveUserPermission($system_user_id, "MANAGE_SETTINGS"))
        $system->printError(403);
    $settings = $system->db()->query("SELECT * FROM `settings` LIMIT 1")->fetch_assoc();
    $content = '../core/template/settings/settings.php';
    include '../core/template/default.php';
}

function profile($args) {
    global $system, $system_user_id, $_user;
    if($system->auth() && $_user['ban'] != 0)
        $system->printError(100);
    $db = $system->db();
    $content = '../core/template/profile/main.php';
    include '../core/template/default.php';
}

function profile_uploads() {
    global $system, $system_user_id, $_user;
    if (!$system->auth())
        Location("/app/auth");
    $db = $system->db();
    $content = '../core/template/profile/uploads.php';
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

function uploads_create() {
    global $system, $system_user_id, $_user;
    if (!$system->haveUserPermission($system_user_id, "CREATE_UPLOADS"))
        $system->printError(403);
    if ($_user['ban_upload'] != 0)
        $system->printError(101);
    $db = $system->db();
    $settings = $db->query("SELECT * FROM `settings` LIMIT 1")->fetch_assoc();
    $content = '../core/template/uploads/create.php';
    include '../core/template/default.php';
}

function uploads_files($args) {
    global $system, $system_user_id, $_user;
    if (!$system->haveUserPermission($system_user_id, "CREATE_UPLOADS"))
        $system->printError(403);
    if ($_user['ban_upload'] != 0)
        $system->printError(101);
    $db = $system->db();
    $settings = $db->query("SELECT * FROM `settings` LIMIT 1")->fetch_assoc();
    $query = $db->query("SELECT * FROM `uploads` WHERE `id` = '".$args['id']."';");
    if($query->num_rows !== 1)
        $system->printError(404);
    $result = $query->fetch_assoc();
    if($result['author'] != $system_user_id)
        $system->printError(403);
    if($result['files'] != "[]")
        exit("Загрузка файлов запрещена");
    $content = '../core/template/uploads/files.php';
    include '../core/template/default.php';
}

function uploads_files_download($args) {
    global $system, $system_user_id, $_user;
    if (!$system->auth())
        $system->printError(103);
    if (!$system->haveUserPermission($system_user_id, "VIEW_UPLOADS"))
        $system->printError(403);
    $db = $system->db();
    $query = $db->query("SELECT * FROM `files` WHERE `id` = '".$args['id']."'");
    if($query->num_rows !== 1)
        $system->printError(404);
    $result = $query->fetch_assoc();
    $upload_id = $result['upload_id'];
    $filename = $result['name'];
    $extension = $result['extension'];
    $query = $db->query("SELECT * FROM `uploads` WHERE `id` = '$upload_id'");
    if($query->num_rows !== 1)
        die("Error: upload not found");
    $result = $query->fetch_assoc();
    $check = ($result['author'] != $system_user_id && !$system->haveUserPermission($system_user_id, "VIEW_HIDDEN_UPLOADS")); // владелец или админ?
    if($result['status'] == 0 || $result['status'] == -1) {
        if($check)
            $system->printError(403);
    }
    
    $file = "../../brigada-miit-storage/".$upload_id."/".$args['id'].".".$extension;
    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }
}

function uploads_view($args) {
    global $system, $system_user_id, $_user;
    /*if (!$system->haveUserPermission($system_user_id, "VIEW_UPLOADS"))
        $system->printError(403);*/
    if($system->auth() && $_user['ban'] != 0)
        $system->printError(100);
    
    header('Content-Type: text/html; charset=utf-8');
    $locale='ru_RU.UTF-8';
    setlocale(LC_ALL, $locale);
    putenv('LC_ALL='.$locale);

    $db = $system->db();
    $query = $db->query("SELECT * FROM `uploads` WHERE `id` = '".$args['id']."'");
    if($query->num_rows !== 1)
        $system->printError(404);
    $result = $query->fetch_assoc();
    $check = ($result['author'] != $system_user_id && !$system->haveUserPermission($system_user_id, "VIEW_HIDDEN_UPLOADS")); // владелец или админ?
    $check2 = ($result['author'] != $system_user_id && !$system->haveUserPermission($system_user_id, "EDIT_ALL_UPLOADS"));
    if($result['status'] == 0 || $result['status'] == -1) {
        if($check)
            $system->printError(403);
    }
    $query_author = $system->db()->query('SELECT * FROM `users` WHERE `id` = "'.$result["author"].'"');
    $result_author = $query_author->fetch_assoc();
    $query_category = $system->db()->query('SELECT * FROM `categories` WHERE `id` = "'.$result['category'].'"');
    $result_category = $query_category->fetch_assoc();
    $content = '../core/template/uploads/view.php';
    include '../core/template/default.php';
}

function uploads_edit($args) {
    global $system, $system_user_id, $_user;
    if (!$system->haveUserPermission($system_user_id, "EDIT_UPLOADS"))
        $system->printError(403);
    if ($_user['ban_upload'] != 0)
        $system->printError(101);
    $db = $system->db();
    $settings = $db->query("SELECT * FROM `settings` LIMIT 1")->fetch_assoc();
    $query = $db->query("SELECT * FROM `uploads` WHERE `id` = '".$args['id']."';");
    if($query->num_rows !== 1)
        $system->printError(404);
    $result = $query->fetch_assoc();
    $check = ($result['author'] != $system_user_id && !$system->haveUserPermission($system_user_id, "EDIT_ALL_UPLOADS"));
    if($check)
        $system->printError(403);
    if($result['status'] == -1) {
        if(!$system->haveUserPermission($system_user_id, "EDIT_ALL_UPLOADS"))
            $system->printError(403);
    }
    $content = '../core/template/uploads/edit.php';
    include '../core/template/default.php';
}

// ================ API ================ \\

function api_main_get_uploads() {
    global $system, $system_user_id, $_user;
    if($system->auth() && $_user['ban'] != 0)
        $system->printError(100);
    $db = $system->db();

    header('Content-Type: text/html; charset=utf-8');
    setlocale(LC_ALL, "ru_RU");
    
    $limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 10; // Количество записей на странице
    if($limit > 100) die("limit should be < 100");
    $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // Номер страницы
    $offset = ($page - 1) * $limit; // Смещение
    $searchTerm = isset($_REQUEST['search']) ? $_REQUEST['search'] : ''; // Термин поиска
    $orderBy = isset($_REQUEST['order']) ? intval($_REQUEST['order']) : 0; // Поле для сортировки
    $orderDir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'DESC'; // Направление сортировки

    switch($orderBy) {
        case 0:
            $order = "id";
            break;
        case 1:
            $order = "name";
            break;
        case 2:
            $order = "created";
            break;
        case 3:
            $order = "author";
            break;
        default:
            $order = "id";
            break;
    }

    $query = $db->query("SELECT COUNT(*) as count FROM `uploads` WHERE `status` = 1");
    if(!$query) die("MySQL error count query");
    $count = $query->fetch_assoc()['count'];

    $query = $db->query("SELECT `id`, `name`, `created`, `author` FROM `uploads`
    WHERE (`name` LIKE '%$searchTerm%' OR `description` LIKE '%$searchTerm%')
    AND `status` = 1
    ORDER BY `$order` $orderDir 
    LIMIT $limit OFFSET $offset");
    if(!$query) die("MySQL error query");

    $data = array();
    if ($query->num_rows > 0) {
        while($row = $query->fetch_assoc()) {
            $user = $system->userinfo(intval($row['author']));
            $username = "Пользователь удалён";
            if($user) {
                $username = "<a style='color: inherit' target='_blank' href='/profile/".$row['author']."'>".$user['surname']." ".$user['lastname']."</a>";
            }
            
            $row['name'] = "<a style='color: inherit' target='_blank' href='/uploads/view/".$row['id']."'>".$row['name']."</a>";
            $row['created'] = "<a style='color: inherit' target='_blank' href='/uploads/view/".$row['id']."'>".unixDateToString(intval($row['created']))."</a>";
            $row['id'] = "<a target='_blank' href='/uploads/view/".$row['id']."'>".$row['id']."</a>";
            $row['author'] = $username;
            $data[] = $row;
        }
    }

    $response = array(
        "count" => intval($count),
        "filtred_count" => ($searchTerm == '') ? intval($count) : $query->num_rows,
        "data" => $data
    );

    echo json_encode($response);
}

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
    if($result['email_verifed'] == 0 || $result['email_verifed'] == '0')
        res(102, $result['email_send_token']);

    $id = $result['id'];
    $solt = bin2hex(openssl_random_pseudo_bytes(20, $cstrong));
    if($id != 0 && !is_null($id)) {
        if($result['ban'] != 0)
            res(4);
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
    if (strlen($lastname) > 25 || strlen($surname) > 25 || strlen($patronymic) > 30)
        res(7);
    if (countWhiteSpaces($lastname) >= 2 || countWhiteSpaces($surname) >= 2 || countWhiteSpaces($patronymic) >= 1)
        res(8);

    $query = $db->query("SELECT * FROM `users` WHERE `email` = '$email'");
    if ($query->num_rows !== 0)
        res(6);

    $passwordHash = $db->real_escape_string(password_hash($password, PASSWORD_DEFAULT));
    $emailVerifyHash = $db->real_escape_string(RandomString(20));
    $emailSendHash = $db->real_escape_string(RandomString(20));
    $time = $db->real_escape_string(time());
    if(!empty($patronymic))
        $query = $db->query("INSERT INTO `users` (`id`, `email`, `password`, `avatar`, `user_type`, `ban`, `ban_upload`, `email_verifed`, `email_token`, `email_send_token`, `email_send_timestamp`, `2fa_secret`, `lastname`, `surname`, `patronymic`, `registred`) VALUES (NULL, '$email', '$passwordHash', '/assets/img/avatar.jpg', 1, 0, 0, 0, '$emailVerifyHash', '$emailSendHash', NULL, NULL, '$lastname', '$surname', '$patronymic', '$time')");
    else
        $query = $db->query("INSERT INTO `users` (`id`, `email`, `password`, `avatar`, `user_type`, `ban`, `ban_upload`, `email_verifed`, `email_token`, `email_send_token`, `email_send_timestamp`, `2fa_secret`, `lastname`, `surname`, `patronymic`, `registred`) VALUES (NULL, '$email', '$passwordHash', '/assets/img/avatar.jpg', 1, 0, 0, 0, '$emailVerifyHash', '$emailSendHash', NULL, NULL, '$lastname', '$surname', NULL, '$time')");
    $query = $db->query("SELECT * FROM `users` WHERE `email` = '$email'");
    if ($query->num_rows !== 1)
        res(7);
    $id = $query->fetch_assoc()['id'];
    $query = $db->query("INSERT INTO `permissions` (`id`, `userid`, `ACCESS`, `MANAGE_USERS`, `MANAGE_SETTINGS`, `VIEW_UPLOADS`, `VIEW_HIDDEN_UPLOADS`, `CREATE_UPLOADS`, `EDIT_UPLOADS`, `EDIT_ALL_UPLOADS`, `DELETE_UPLOADS`, `DELETE_ALL_UPLOADS`, `MANAGE_CATEGORIES`) VALUES (NULL, '$id', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0')");
    $system->send_email_verification($emailSendHash);
    res(1);
}

function api_email_resend($args) {
    global $system;
    $token = $args['token'];
    $link = $system->db()->query("SELECT * FROM `settings` LIMIT 1")->fetch_assoc()['link_to_admin'];
    $verification = $system->send_email_verification($token);
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
    $db->query("UPDATE `users` SET `email_verifed` = '1' WHERE `users`.`email_token` = '$token';");
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

function api_users_edit() {
    global $system, $system_user_id, $_user;
    if(!$system->haveUserPermission($system_user_id, "MANAGE_USERS"))
        res(0, "Ошибка доступа");

    $user_id = !empty(intval($_POST['id'])) ? intval($_POST['id']) : res(0, "user_id error 1");
    if (!$user = $system->userinfo($user_id))
        res(0, "Ошибка");
    if(!is_numeric($_POST['role']))
        res(4, "Выберите роль и попробуйте снова");

    $role = !empty(intval($_POST['role'])) || $_POST['role'] < 1 ? intval($_POST['role']) : 1;
    if($role < 1) res(0, "Неправильно задана роль");
    $user_role = $system->userinfo()['user_type'];
    if ($user_role <= $user['user_type'] || $role >= $user_role)
        res(0, "Ваша роль ниже чем у данного пользователя");

    $db = $system->db();
    $db->set_charset("utf8");

    $ban = (intval($_POST['ban']) == 0 || intval($_POST['ban']) == 1) ? intval($_POST['ban']) : res(0, "ban error 1");
    if($ban == 1) 
        $ban = $system_user_id;
    else if($ban == 0);
    else res(0, "ban error 2");

    $ban_upload = (intval($_POST['ban_upload']) == 0 || intval($_POST['ban_upload']) == 1) ? intval($_POST['ban_upload']) : res(0, "ban_upload error 1");
    if($ban_upload == 1) 
        $ban_upload = $system_user_id;
    else if($ban_upload == 0);
    else res(0, "ban_upload error 2");

    $email_verifed = (intval($_POST['email_verifed']) == 0 || intval($_POST['email_verifed']) == 1) ? intval($_POST['email_verifed']) : res(0, "email_verifed error 1");
    if($email_verifed == 1) {
        if(intval($user['email_verifed']) == 0)
            $email_verifed = $system_user_id;
    }
    else if($email_verifed == 0) {
        if(intval($user['email_verifed']) != 0)
            res(0, "email_verifed error 2");
    }
    
    $patr_check = 0;

    $lastname = !empty($_POST['lastname']) ? $_POST['lastname'] : res(0, "Укажите фамилию");
    $surname = !empty($_POST['surname']) ? $_POST['surname'] : res(0, "Укажите имя");
    $patronymic = !empty($_POST['patronymic']) ? $_POST['patronymic'] : ($patr_check = 1);

    if (strlen($lastname) > 25 || strlen($surname) > 25 || strlen($patronymic) > 30)
        res(0, "В полях ФИО слишком много символов");
    if (countWhiteSpaces($lastname) >= 2 || countWhiteSpaces($surname) >= 2 || countWhiteSpaces($patronymic) >= 1)
        res(0, "В полях ФИО слишком много пробелов");

    if(!$patr_check)
        $query = $db->query("UPDATE `users` SET `user_type` = '$role', `ban` = '$ban', `ban_upload` = '$ban_upload', `email_verifed` = '$email_verifed', `lastname` = '$lastname', `surname` = '$surname', `patronymic` = '$patronymic' WHERE `id` = '$user_id'");
    else
        $query = $db->query("UPDATE `users` SET `user_type` = '$role', `ban` = '$ban', `ban_upload` = '$ban_upload', `email_verifed` = '$email_verifed', `lastname` = '$lastname', `surname` = '$surname', `patronymic` = NULL WHERE `id` = '$user_id'");
    
    if(!$query) res(0, "mysql error");
    res(1, "Данные пользователя успешно обновлены");
}

function api_users_delete() {
    global $system, $system_user_id, $_user;
    if(!$system->haveUserPermission($system_user_id, "MANAGE_USERS"))
        res(0, "Ошибка доступа");
    $id = intval($_POST['id']) > 0 ? intval($_POST['id']) : res(0, 'Выберите пользователя');
    $user_role = $system->userinfo()['user_type'];
    $email = $system->userinfo($id)['email'];
    if ($user_role <= $system->userinfo($id)['user_type'])
        res(0, "Ваша роль меньше или равна удаляемому пользователю");
    $system->db()->query("INSERT INTO `users_deleted` SELECT * FROM `users` WHERE `id` = '$id'");
    $system->db()->query("DELETE FROM `users` WHERE `id` = '$id'");
    $system->db()->query("DELETE FROM `users_session` WHERE `id` = '$id'");
    res(1, "Пользователь ". $email . " успешно удален");
}

function api_users_permissions() {
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
            $db->query("UPDATE `permissions` SET ".$data[$i][0]." = 1 WHERE `userid` = $user_id");
        }
        else if(!$data[$i][1] && $system->haveUserPermission($_user['id'], $data[$i][0])) {
            $db->query("UPDATE `permissions` SET ".$data[$i][0]." = 0 WHERE `userid` = $user_id");
        }
    }
    res(1, "Права успешно обновлены!");
}

function api_profile_get_uploads() {

}

function api_profile_get_uploads_self() {
    global $system, $system_user_id, $_user;
    if(!$system->auth())
        res(0);
    if($_user['ban'] != 0)
        $system->printError(100);
    $db = $system->db();

    header('Content-Type: text/html; charset=utf-8');
    setlocale(LC_ALL, "ru_RU");
    
    $limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 10; // Количество записей на странице
    if($limit > 100) die("limit should be < 100");
    $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // Номер страницы
    $offset = ($page - 1) * $limit; // Смещение
    $searchTerm = isset($_REQUEST['search']) ? $_REQUEST['search'] : ''; // Термин поиска
    $orderBy = isset($_REQUEST['order']) ? intval($_REQUEST['order']) : 0; // Поле для сортировки
    $orderDir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'DESC'; // Направление сортировки

    switch($orderBy) {
        case 0:
            $order = "id";
            break;
        case 1:
            $order = "name";
            break;
        case 2:
            $order = "created";
            break;
        case 3:
            $order = "status";
            break;
        default:
            $order = "id";
            break;
    }

    $query = $db->query("SELECT COUNT(*) as count FROM `uploads` WHERE `author` = $system_user_id");
    if(!$query) die("MySQL error count query");
    $count = $query->fetch_assoc()['count'];

    $query = $db->query("SELECT `id`, `name`, `created`, `author` FROM `uploads`
    WHERE (`name` LIKE '%$searchTerm%' OR `description` LIKE '%$searchTerm%')
    AND `author` = $system_user_id
    ORDER BY `$order` $orderDir 
    LIMIT $limit OFFSET $offset");
    if(!$query) die("MySQL error query");

    $data = array();
    if ($query->num_rows > 0) {
        while($row = $query->fetch_assoc()) {
            $row['name'] = "<a style='color: inherit' target='_blank' href='/uploads/view/".$row['id']."'>".$row['name']."</a>";
            $row['created'] = "<a style='color: inherit' target='_blank' href='/uploads/view/".$row['id']."'>".unixDateToString(intval($row['created']))."</a>";
            $row['id'] = "<a target='_blank' href='/uploads/view/".$row['id']."'>".$row['id']."</a>";
            $row['status'] = intval($row['status']);
            $row['status'] = ($row['status'] != -1 ? (($row['status'] == 1) ? "Опубликовано" : "Не опубликовано") : "Скрыто администратором");
            $data[] = $row;
        }
    }

    $response = array(
        "count" => intval($count),
        "filtred_count" => ($searchTerm == '') ? intval($count) : $query->num_rows,
        "data" => $data
    );

    echo json_encode($response);
}

function api_settings_update() {
    global $system, $system_user_id, $_user;
    if (!$system->haveUserPermission($system_user_id, "MANAGE_SETTINGS"))
        res(0, "Ошибка доступа");
    $max_size_avatar = intval($_POST['max_size_avatar']) > 0 ? intval($_POST['max_size_avatar']) : res(0, 'Укажите целое положительное число (max_size_avatar)');
    $link_to_admin = empty($_POST['link_to_admin']) ? res(0, "Укажите ссылку на администратора") : $_POST['link_to_admin'];
    $max_size_file = intval($_POST['max_size_file']) > 0 ? intval($_POST['max_size_file']) : res(0, 'Укажите целое положительное число (max_size_file)');
    $system->db()->query("UPDATE `settings` SET `max_size_avatar` = '$max_size_avatar', `link_to_admin` = '$link_to_admin', `max_size_file` = '$max_size_file' WHERE 1");
    res(1, "Настройки успешно обновлены");
}

function api_user_changepassword() {
    global $system, $system_user_id, $_user;
    if (!$system->auth())
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

function api_uploads_create() {
    global $system, $system_user_id, $_user;
    if(!$system->haveUserPermission($system_user_id, "CREATE_UPLOADS")) res(0);
    if ($_user['ban_upload'] != 0)
        $system->printError(101);
    if(empty($_POST['name']) || empty($_POST['description']) || empty($_POST['category']))
        res(0, "Invalid request");
    $status = 0; // 0 - неопубликован, так как ещё не приложены файлы

    $category = $_POST['category']; // проверка на категорию через БД
    $db = $system->db();
    $query = $db->query("SELECT * FROM `categories` WHERE `id` = $category");
    if(!$query || $query->num_rows == 0) res(0, 'error categories');
    $result = $query->fetch_assoc();
    if($result['status'] == 0) res(0, 'category is hidden');

    $timestamp = time();
    $query = $db->query("INSERT INTO `uploads` (`id`, `author`, `name`, `description`, `category`, `status`, `files`, `created`, `uploaded`, `updated`) VALUES (NULL, '$system_user_id', '".$_POST['name']."', '".$_POST['description']."', '$category', '$status', '[]', '$timestamp', '0', '0')");
    if(!$query) res(0, 'MySQL error');
    $query = $db->query("SELECT `id` FROM `uploads` ORDER BY ID DESC LIMIT 1");
    $result = $query->fetch_assoc();
    $upload_id = $result['id'];
    res(1, $upload_id);
}

function api_uploads_edit($args) {
    global $system, $system_user_id, $_user;
    if(!$system->haveUserPermission($system_user_id, "EDIT_UPLOADS")) res(0);
    if(empty($_POST['name']) || empty($_POST['description']) || empty($_POST['category']) || ($_POST['status'] != 0 && $_POST['status'] != 1 && $_POST['status'] != -1))
        res(0, "Invalid request");
    if ($_user['ban_upload'] != 0)
        res(0, "ban upload");

    $id = $args['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];

    $db = $system->db();
    $query = $db->query("SELECT * FROM `uploads` WHERE `id` = $id");
    if(!$query || $query->num_rows == 0) res(0, 'error uploads');
    $result = $query->fetch_assoc();
    $check = ($result['author'] != $system_user_id && !$system->haveUserPermission($system_user_id, "EDIT_ALL_UPLOADS"));
    if($check)
        res(0);
    if($result['files'] == "[]")
        res(2);
    
    $status = $_POST['status']; // проверка на статус (0, 1, -1)
    if($status == 0 || $status == 1 || $status == -1) {
        if($status == -1) {
            if(!$system->haveUserPermission($system_user_id, "EDIT_ALL_UPLOADS"))
                res(100, "no permission to hide");
        }
    }
    else res(0, "status error");

    $category = $_POST['category']; // проверка на категорию через БД
    $query = $db->query("SELECT * FROM `categories` WHERE `id` = $category");
    if(!$query || $query->num_rows == 0) res(0, 'error categories');
    $resultc = $query->fetch_assoc();
    if($resultc['status'] == 0) res(0, 'category is hidden');

    $timestamp = time();
    $query = $db->query("UPDATE `uploads` SET `name` = '$name', `description` = '$description', `category` = '$category', `status` = '$status' WHERE `uploads`.`id` = $id");
    if(!$query) res(0, 'MySQL error');
    res(1, $id);
}

function api_uploads_delete($args) {
    global $system, $system_user_id, $_user;
    if(!$system->haveUserPermission($system_user_id, "DELETE_UPLOADS")) res(0);
    if ($_user['ban_upload'] != 0)
        res(0, "ban upload");
    $id = $args['id'];
    $db = $system->db();
    $query = $db->query("SELECT * FROM `uploads` WHERE `id` = $id");
    if(!$query || $query->num_rows == 0) res(0, 'error uploads');
    $result = $query->fetch_assoc();
    $check = ($result['author'] != $system_user_id && !$system->haveUserPermission($system_user_id, "DELETE_ALL_UPLOADS"));
    if($check)
        res(0);
    if($result['status'] == -1) {
        if(!$system->haveUserPermission($system_user_id, "DELETE_ALL_UPLOADS"))
            res(0, "forbidden (because hidden)");
    }
    $db->query("INSERT INTO `uploads_deleted` SELECT * FROM `uploads` WHERE `id` = '$id'");
    $db->query("DELETE FROM `uploads` WHERE `id` = '$id'");
    res(1);
}

function api_files_upload() {
    global $system, $system_user_id, $_user;
    if(!$system->haveUserPermission($system_user_id, "CREATE_UPLOADS")) res(0);
    if($_user['ban_upload'] != 0) res(0, "ban upload");
    $uploadDir = '../../brigada-miit-storage/';
    $fileTypes = array('jpg', 'jpeg', 'gif', 'png', 'docx', 'doc', 'txt', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'zip');
    $verifyToken = md5('unique_salt' . $_POST['timestamp']);
    if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
        $upload_id = $_POST['id'];
        if(empty($upload_id)) 
            res(0, "Invalid id");
        $db = $system->db();
        $query = $db->query("SELECT * FROM `uploads` WHERE `id` = '$upload_id'");
        if($query->num_rows !== 1)
            res(0, "Upload not found");
        $result = $query->fetch_assoc();
        if($result['author'] != $system_user_id)
            $system->printError(403);
        if($result['status'] != 0)
            res(0, "Upload is forbidden");
        $files = json_decode($result['files']);
        if(count($files) > 9)
            res(0, "Upload is forbidden");

        $uploadDir = $uploadDir . $upload_id;
        if(is_dir($uploadDir . '/')) {
            if((count(scandir($uploadDir . '/')) - 2) > 9)
                res(0, "Upload is forbidden");
        }
        else {
            if (!mkdir($uploadDir . '/', 0777, true))
                res(0, "Error: New directory wasn't created");
        }

        $extension = substr($_FILES['Filedata']['name'],strripos($_FILES['Filedata']['name'],'.')+1);

        $query = $db->query("INSERT INTO `files` (`id`, `upload_id`, `name`, `extension`, `path`, `size`, `token`) VALUES (NULL, '$upload_id', '".$_FILES['Filedata']['name']."', '$extension', '0', '".$_FILES['Filedata']['size']."', '".$_POST['token']."')");
        if(!$query) exit('MySQL error');
        $query = $db->query("SELECT `id` FROM `files` ORDER BY ID DESC LIMIT 1");
        $result = $query->fetch_assoc();
        $file_id = $result['id'];
        
        array_push($files, array("id" => $file_id, "name" => $_FILES['Filedata']['name'], "size" => $_FILES['Filedata']['size']));
        $files = json_encode($files, JSON_UNESCAPED_UNICODE);
        $db->set_charset("utf8");
        $query = $db->query("UPDATE `uploads` SET `files` = '$files' WHERE `uploads`.`id` = $upload_id;");
        if(!$query)
            res(0, "MySQL error (query updating upload)");

        $tempFile   = $_FILES['Filedata']['tmp_name'];
        $targetFile = $uploadDir . '/' . $file_id . '.' . $extension;
        $fileParts = pathinfo($_FILES['Filedata']['name']);
        $settings = $system->db()->query("SELECT * FROM `settings` LIMIT 1")->fetch_assoc();
        if($_FILES['Filedata']['size'] > $settings['max_size_file'] * 1048576) {
            res(0, "Size cannot be > 50 MB");
        }
        if(in_array(strtolower($fileParts['extension']), $fileTypes)) {
            move_uploaded_file($tempFile, $targetFile);
            echo 1;
        } 
        else
            res(0, "Invalid file type");
    }
}

function api_files_upload_check() {
    $targetFolder = '../../brigada-miit-storage';
    if (file_exists($targetFolder . "/" . $_POST['filename']))
        echo 1;
    else
        echo 0;
}