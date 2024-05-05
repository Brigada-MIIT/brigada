<div class="container" style="padding-top: 2%;">  
    <div class="form">
        <div class="col-12">
            <div class="in">
                <div class="text-center">
                    <h2 class="active">Регистрация</h2>
                </div><br>
            </div>
        </div>
        <div class="col-12">
            <div class="in" style="margin: 0 auto; width: max-content;">
                <label for="email">Email</label><br>
                <input type="text" class="text" id="email" placeholder="Введите адрес..." style="width:350px; height:37px;"><br><br>
            </div>
        </div>
        <div class="col-12">
            <div class="in" style="margin: 0 auto; width: max-content;">
                <label for="password-field">Пароль</label><br>
                <input type="password" class="text" id="password-field" placeholder="Введите пароль..." style="width:350px; height:37px;"><br>
                
            </div>
        </div>
        <div class="col-12">
            <div class="in" style="padding-top: 10px; margin: 0 auto; width: max-content;">
                <label for="password-repeat-field">Повторите пароль</label><br>
                <input type="password" class="text" id="password-repeat-field" placeholder="Повторите пароль..." style="width:350px; height:37px;"><br>
            </div>
        </div>
        <div class="col-12">
            <div class="in" style="padding-top: 25px; margin: 0 auto; width: max-content;">
                <label for="lastname">Фамилия</label><br>
                <input type="text" class="text" id="lastname" placeholder="Введите фамилию..." style="width:350px; height:37px;"><br>
            </div>
        </div>
        <div class="col-12">
            <div class="in" style="padding-top: 10px; margin: 0 auto; width: max-content;">
                <label for="surname">Имя</label><br>
                <input type="password" class="text" id="surname" placeholder="Введите имя..." style="width:350px; height:37px;"><br>
            </div>
        </div>
        <div class="col-12">
            <div class="in" style="padding-top: 10px; margin: 0 auto; width: max-content;">
                <label for="patronymic">Отчество</label><br>
                <input type="password" class="text" id="patronymic" placeholder="Введите отчество..." style="width:350px; height:37px;"><br>
            </div>
        </div>
        <div class="col-12 text-center">
            <div class="in">
                <br><button type="submit" class="submit text-center" onclick="register();">Зарегистрироваться</button>
            </div>
        </div>
    </div>
</div>
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
            register();
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