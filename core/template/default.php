<!doctype html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="/assets/css/jquery.datetimepicker.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/main.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/dashboard.css">
    <link rel="SHORTCUT ICON" href="/assets/img/logo_brigada.ico" type="image/x-icon">
    <title><?php echo ($title) ? $title : "Бригада"; ?></title>
    <script src="/assets/js/jquery.min.js"></script>
    <!--<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>-->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="/assets/js/sweetalert2.js"></script>
  </head>
  <body>
    <div class="wrapper">
        <header>
            <nav class="navbar navbar-expand-md navbar-light bg-light">
                <a class="navbar-brand" href="#">
                    <img src="/assets/img/logo_brigada_min.png" width="32" height="32" alt="">
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item active">
                            <a class="nav-link" href="/">Главная <span class="sr-only">(current)</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Категории</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        <?php if(!$system->auth()) echo '
                        <hr class="d-sm-none">
                        <li class="nav-item">
                            <a class="nav-link" href="/app/auth">Войти</a>
                        </li>
                        <p class="d-none d-md-block" style="color: rgba(0, 0, 0, .5);">|</p>
                        <li class="nav-item">
                            <a class="nav-link" href="/app/register">Регистрация</a>
                        </li>';
                        if($system->auth()) echo '
                        <hr class="d-sm-none">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16" style="top: 0.13em;position: relative"><path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/><path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/></svg>
                                '. $_user['surname'] .'
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink" style="left: -55%;">
                                <a class="dropdown-item" href="/profile">Профиль</a>
                                <a class="dropdown-item" href="/profile/uploads">Мои загрузки</a>';
                            if($system->haveUserPermission($system_user_id, "CREATE_UPLOADS") && $system->userinfo($system_user_id)['ban_upload'] == 0) echo '
                                <a class="dropdown-item" href="/uploads/create">Создать загрузку</a>';
                            if($system->haveUserPermission($system_user_id, "MANAGE_USERS") || $system->haveUserPermission($system_user_id, "MANAGE_SETTINGS")) echo '
                                <hr style="margin-top: 5px;margin-bottom: 4px;">';
                            if($system->haveUserPermission($system_user_id, "MANAGE_USERS")) echo '
                                <a class="dropdown-item" href="/app/users">Пользователи</a>';
                            if($system->haveUserPermission($system_user_id, "MANAGE_SETTINGS")) echo '
                                <a class="dropdown-item" href="/app/settings">Настройки</a>';
                            if($system->auth()) echo '
                                <hr style="margin-top: 5px;margin-bottom: 4px;">
                                <a class="dropdown-item" href="/logout">Выйти</a>
                            </div>
                        </li>'; ?>
                    </ul>
                </div>
            </nav>
        </header>
        <main>
            <?php include $content;?>
        </main>
    </div>
  </body>
</html>