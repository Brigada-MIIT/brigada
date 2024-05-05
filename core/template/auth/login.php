<div class="container" style="padding-top: 6%;">  
    <!--<p class="page-title">Настройки</p>-->
    <div class="form">
        <div class="col-12">
            <div class="in">
                <div class="text-center">
                    <h2 class="active">Вход</h2>
                </div><br>
            </div>
        </div>
        <div class="col-12">
            <div class="in" style="margin: 0 auto; width: max-content;">
                <label for="login">Email</label><br>
                <input type="text" class="text" id="login" placeholder="Введите адрес..." style="width:350px; height:40px;"><br><br>
                <label for="password">Пароль</label><br>
                <input type="password" class="text" id="password" placeholder="Введите пароль..." style="width:350px; height:40px;"><br>
            </div>
        </div>
        <div class="col-12 text-center">
            <div class="in">
                <br><button type="submit" class="submit text-center" onclick="login();">Войти</button>
            </div>
        </div>
    </div>
</div>
<script>
    function login(code) {
        let a_code = ""
        if(code) {
            a_code = '&auth_code='+code
        }
        Swal.close();
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
                    text: 'Если вы не получили письмо, нажмите снизу "Переотправить письмо"',
                    footer: '<a onclick="resendEmail(\'' + res.text + '\');" href="#">Переотправить письмо</a>&nbsp;|&nbsp;<a href="<?php echo $link_to_admin ?>">Возникли вопросы?</a>'
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

    function resendEmail(token) {
        Swal.close();
        $.ajax({
            type: 'POST',
            url: '/email/resend/'+token,
            success: async function(data) {
            console.log('/email/resend/'+token);
            var res = $.parseJSON(data);
            if (res.result == 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Ошибка',
                    text: 'При отправке письма произошла неизвестная ошибка. Обратитесь к администратору.',
                    footer: '<a href="<?php echo $link_to_admin ?>">Возникли вопросы?</a>'
                });
            } else if (res.result == 1) {
                Swal.fire({
                    icon: 'success',
                    title: 'Письмо успешно оптравлено',
                    text: 'На указанный Email-адрес было отправлено письмо с ссылкой для подтверждения вашего аккаунта',
                    footer: '<a href="<?php echo $link_to_admin ?>">Возникли вопросы?</a>'
                });
            } else if (res.result == 2) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Письмо не было отправлено',
                    text: 'Переотправка письма возможна 1 раз в 5 минут. Попробуйте переотправить письмо чуть позже снова.',
                    footer: '<a href="<?php echo $link_to_admin ?>">Возникли вопросы?</a>'
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
            if(!Swal.isVisible()) login();
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