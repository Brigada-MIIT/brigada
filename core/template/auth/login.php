<html lang="en">
<head>
    <title>Авторизация</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="/assets/img/logo_logs_notepad.ico">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/assets/css/login.css">
    <script src="/assets/js/jquery.min.js"></script>
    <!--<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>-->
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
                        <h3 class="mb-4 text-center">Brigada Dashboard</h3>
                        <div action="#" class="signin-form">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Username" id="email" required>
                            </div>
                            <div class="form-group">
                                <input id="password-field" type="password" class="form-control" placeholder="Password" required>
                                <span toggle="#password-field" id="btn-pswd" class="fa fa-fw field-icon toggle-password fa-eye"></span>
                            </div>
                            <div class="form-group" style="padding-top: 3%">
                                <button type="submit" class="form-control btn btn-primary submit px-3" onclick="login();">Войти</button>
                            </div>
                            <div class="form-group d-md-flex">
                                <div class="w-50" hidden>
                                    <label class="checkbox-wrap checkbox-primary">Remember Me
                                        <input type="checkbox" checked="">
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <div class="w-50 text-md-right">
                                    <a href="#" style="color: #fff" hidden>Forgot Password</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        function login(code) {
            let a_code = ""
            if(code) {
                a_code = '&auth_code='+code
            }
            $.ajax({
              type: 'POST',
              url: '/api/login',
              data: 'email='+$("#email").val()+'&password='+$("#password-field").val()+a_code,
              success: async function(data) {
                var res = $.parseJSON(data);
                if (res.result == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Неверный пароль!',
                        text: 'Проверьте правильность введённых данных.',
                        footer: '<a href="<?php echo $link_to_admin ?>">Забыли пароль?</a>'
                    });
                } else if (res.result == 1) {
                    $(".res").html('');
                    location.replace("/");
                } else if (res.result == 4) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ваш аккаунт заблокирован!',
                        text: 'Обратитесь к администратору.',
                        footer: '<a href="<?php echo $link_to_admin ?>">Возникли вопросы?</a>'
                    });
                } else if (res.result == 100) {
                    const { value: user_code } = await Swal.fire({
                        icon: 'warning',
                        input: 'text',
                        title: '2FA Code',
                        inputPlaceholder: 'Укажите ваш 2FA код для авторизации',
                        inputAttributes: {
                            'aria-label': 'Type your message here'
                        },
                        showCancelButton: true
                    })
                    await login(user_code);
                } else if (res.result == 101) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Неверный код 2FA!',
                        text: 'Попробуйте снова или обратитесь к администратору.',
                        footer: '<a href="<?php echo $link_to_admin ?>">Возникли вопросы?</a>'
                    });
                } else if (res.result == 102) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Не подтверждён Email-адрес',
                        text: 'Вы не подтвердили Email-адрес. Если вы не получили письмо, нажмите снизу "Переотправить письмо"',
                        footer: '<a>Переотправить письмо</a>&nbsp;|&nbsp;<a href="<?php echo $link_to_admin ?>">Возникли вопросы?</a>'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Произошла неизвестная ошибка!',
                        text: 'Обратитесь к администратору.',
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
            }
            else {
                $('#password-field').attr('type', 'password');
            }
            $('span#btn-pswd').toggleClass("fa-eye");
            $('span#btn-pswd').toggleClass("fa-eye-slash");
            show_pswd = !show_pswd

        });
    </script>
</body>
</html>