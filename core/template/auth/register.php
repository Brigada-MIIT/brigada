<html lang="en">
<head>
    <title>Регистрация</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="/assets/img/logo_logs_notepad.ico">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/assets/css/login.css">
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/sweetalert2.js"></script>
</head>
<?php
    $link_to_admin = $settings['link_to_admin'];
?>
<body class="img js-fullheight" style="background-color: #112211; /*background-image: url('/assets/img/2022-05-14_23.50.55.png');*/">
    <section class="ftco-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 text-center mb-5">
                    <h2 class="heading-section"></h2>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="login-wrap p-0">
                        <h3 class="mb-4 text-center">Регистрация</h3>
                        <div action="#" class="signin-form">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Email" id="email" required>
                            </div>
                            <hr>
                            <div class="form-group">
                                <input id="password-field" type="password" class="form-control" placeholder="Password" required>
                                <span toggle="#password-field" id="btn-pswd" class="fa fa-fw field-icon toggle-password fa-eye"></span>
                            </div>
                            <div class="form-group">
                                <input id="password-repeat-field" type="password" class="form-control" placeholder="Repeat Password" required>
                                <span toggle="#password-field" id="btn-pswd" class="fa fa-fw field-icon toggle-password fa-eye"></span>
                            </div>
                            <hr>
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Фамилия" id="lastname" required>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Имя" id="surname" required>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Отчество" id="patronymic">
                            </div>
                            <div class="form-group" style="padding-top: 3%">
                                <button type="submit" class="form-control btn btn-primary submit px-3" onclick="register();">Register</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        function register() {
            $.ajax({
              type: 'POST',
              url: '/api/register',
              data: 'email='+$("#email").val()+'&password='+$("#password-field").val()+'&password_repeat='+$("#password-repeat-field").val()+'&lastname='+$("#lastname").val()+'&surname='+$("#surname").val()+'&patronymic='+$("#patronymic").val(),
              success: async function(data) {
                var res = $.parseJSON(data);
                if (res.result == 1) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Успешная регистрация',
                        text: 'На указанный Email-адрес было отправлено письмо с ссылкой для подтверждения вашего аккаунта',
                        footer: '<a href="<?php echo $link_to_admin ?>">Возникли вопросы?</a>'
                    });
                } else if (res.result == 2) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Указан неверный Email-адрес',
                        text: 'Укажите, пожалуйста, правильный Email-адрес',
                        footer: '<a href="<?php echo $link_to_admin ?>">Возникли вопросы?</a>'
                    });
                } else if (res.result == 3) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Пароли не совпадают',
                        text: 'Перепроверьте, пожалуйста, совпадение паролей в полях',
                        footer: '<a href="<?php echo $link_to_admin ?>">Возникли вопросы?</a>'
                    });
                } else if (res.result == 4) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Пароль меньше 6-ти символов',
                        text: 'Укажите, пожалуйста, пароль не менее 6-ти символов',
                        footer: '<a href="<?php echo $link_to_admin ?>">Возникли вопросы?</a>'
                    });
                } else if (res.result == 5) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Не заполнены поля фамилия/имя',
                        text: 'Пожалуйста, заполните следующие поля: фамилия, имя, отчество',
                        footer: '<a href="<?php echo $link_to_admin ?>">Возникли вопросы?</a>'
                    });
                } else if (res.result == 6) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Пользователь уже существует',
                        text: 'Пользователь с указанным Email-адресом уже существует',
                        footer: '<a href="<?php echo $link_to_admin ?>">Забыли пароль?</a>'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Произошла неизвестная ошибка!',
                        footer: '<a href="<?php echo $link_to_admin ?>">Возникли вопросы?</a>'
                    });
                }
            }
            });
        }
        
        document.addEventListener('keypress', function(event) {
            if(arguments[0].code == "Enter" || arguments[0].code == "NumpadEnter") {
                login();
            }
        });

        let show_pswd = false;

        $('span#btn-pswd').click(function() {
            if(!show_pswd) {
                $('#password-field').attr('type', 'text');
                $('#password-repeat-field').attr('type', 'text');
            }
            else {
                $('#password-field').attr('type', 'password');
                $('#password-repeat-field').attr('type', 'password');
            }
            $('span#btn-pswd').toggleClass("fa-eye");
            $('span#btn-pswd').toggleClass("fa-eye-slash");
            show_pswd = !show_pswd

        });
    </script>
</body>
</html>