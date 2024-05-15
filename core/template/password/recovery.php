<div class="container">  
    <p class="page-title">Восстановление пароля</p>
    <h4>Вы на странице восстановления пароля. Пожалуйста, введите ваш Email-адрес.
        <br>В  случае, если аккаунт существует, на указанный адрес будет выслана инструкция по смене пароля.
    </h4>
    <div class="form">
        <div class="col-12">
            <div class="in">
                <label for="email">Email</label><br>
                <input type="text" class="text" id="email" <?php if(!empty($_REQUEST['email'])) echo "value='".$_REQUEST['email']."'" ?> placeholder="Введите адрес...">
            </div>
        </div>
        <div class="col-12">
            <div class="in">
                <br><button id="submit" type="submit" class="submit" onclick="submit();">Восстановить пароль</button>
            </div>
        </div>
    </div>
</div>
<script>
    let action = true;

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: true,
        timer: 5000,
        timerProgressBar: true
    });

    function submit() {
        if(!document.getElementById('email').value)
            return Toast.fire({
                icon: 'error',
                title: 'Заполните, пожалуйста, все поля'
            });
        if (!action) return;
        action = false;
        $.ajax({
            type: 'POST',
            url: '/api/password/recovery',
            data: 'email='+$("#email").val(),
            success: async function(data) {
                var res = $.parseJSON(data);
                console.log(res);
                if (res.result == 1) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Успешно!',
                        text: 'Ваш электронная почта принята! Если аккаунт с таким электронным адресом существует, то на него будет выслана инструкция по восстановлению пароля',
                        footer: '<a href="<?php echo $settings['link_to_admin'] ?>">Возникли вопросы?</a>'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.replace("/app/auth");
                        }
                    });
                    document.getElementById('submit').onclick = "";
                    document.getElementById('email').disabled = true;
                } else if (res.result == 2) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Заполните, пожалуйста, все поля'
                    });
                    action = true;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Произошла неизвестная ошибка!',
                        text: 'Обратитесь к администратору.',
                        footer: '<a href="<?php echo $settings['link_to_admin'] ?>">Возникли вопросы?</a>'
                    });
                    action = true;
                }
            }
        });
    }
</script>   