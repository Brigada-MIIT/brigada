<?php
header('Access-Control-Allow-Origin: *');
$system = new System();

require __DIR__ . '/vendor/autoload.php';

$_user = $system->userinfo();
$system_user_id = $system->userinfo()['id'];

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', 'main');
    $r->addRoute('GET', '/app/auth', 'auth');
    $r->addRoute('GET', '/app/register', 'register');
    $r->addRoute('GET', '/users', 'users'); // ***
    $r->addRoute('GET', '/users/create', 'users_add');
    $r->addRoute('GET', '/users/edit/{id:\d+}', 'users_edit');
    $r->addRoute('GET', '/users/delete/{id:\d+}', 'users_delete');
    $r->addRoute('GET', '/app/settings', 'settings');
    $r->addRoute('GET', '/profile/password', 'profile_password');
    $r->addRoute('GET', '/profile/avatar', 'profile_avatar');
    $r->addRoute(['GET', 'POST'], '/uploads/create', 'uploads_create');
    $r->addRoute('GET', '/uploads/files/{id:\d+}', 'uploads_files');
    $r->addRoute('GET', '/uploads/files/download/{id:\d+}', 'uploads_files_download');
    $r->addRoute('GET', '/uploads/view/{id:\d+}', 'uploads_view');
    $r->addRoute('GET', '/uploads/edit/{id:\d+}', 'uploads_edit');
    //$r->addRoute('GET', '/uploads/delete/{id:\d+}', 'uploads_delete');
    //*** API ***\\
    $r->addRoute('POST', '/api/login', 'api_login');
    $r->addRoute('GET', '/api/login/get', 'api_login');
    $r->addRoute('POST', '/api/register', 'api_register');
    $r->addRoute('POST', '/email/resend/{token}', 'api_email_resend');
    $r->addRoute('GET', '/email/verify/{token}', 'api_email_verify');
    $r->addRoute('GET', '/logout', 'logout');
    $r->addRoute('POST', '/api/users/add', 'api_users_add');
    $r->addRoute('POST', '/api/user/edit', 'api_users_edit');
    $r->addRoute('POST', '/api/users/delete', 'api_users_delete');
    $r->addRoute('POST', '/api/user/permissions', 'api_user_permissions');
    $r->addRoute('POST', '/api/settings/update', 'api_settings_update');
    $r->addRoute('POST', '/api/uploads/create', 'api_uploads_create');
    $r->addRoute('POST', '/api/uploads/edit/{id:\d+}', 'api_uploads_edit');
    $r->addRoute('POST', '/api/uploads/delete/{id:\d+}', 'api_uploads_delete');
    $r->addRoute('POST', '/api/files/upload', 'api_files_upload');
    $r->addRoute('POST', '/api/files/upload/check', 'api_files_upload_check');
    //$r->addRoute('GET', '/api/test', 'api_test'); // TEST УДАЛИТЬ
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

include __DIR__ . "/handlers.php";

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        $system->printError(404);
        die();
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        $system->printError(405);
        die();
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        print $handler($vars);
        break;
}