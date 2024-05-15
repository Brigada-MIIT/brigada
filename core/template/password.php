<div class="container">  
    <p class="page-title">Изменение пароля для <?php echo $result['email'] ?></p>
    <div class="form">
        <div class="col-12">
            <div class="in">
                <label for="password">Новый пароль</label><br>
                <input type="password" class="text" id="password" placeholder="Введите пароль...">
            </div>
        </div>
        <div class="col-12">
        <div class="in">
                <label for="password_repeat">Повторите пароль</label><br>
                <input type="password" class="text" id="password_repeat" placeholder="Повторите пароль...">
            </div>
        </div>
        <div class="col-12">
            <div class="in">
                <br><button id="submit" type="submit" class="submit" onclick="submit();">Изменить пароль</button>
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
        if(!document.getElementById('name').value || !document.getElementById('description').value)
            return Toast.fire({
                icon: 'error',
                title: 'Заполните, пожалуйста, все поля'
            });
        if (!action) return;
        action = false;
        $.ajax({
            type: 'POST',
            url: '/api/password/change/<?php echo $token ?>',
            data: 'password='+document.getElementById('password').value+'&password_repeat='+document.getElementById('password_repeat').value,
            success: async function(data) {
                var res = $.parseJSON(data);
                console.log(res);
                if (res.result == 1) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Успешно!',
                        text: 'Пароль от вашего аккаунта был успешно изменён! После смены пароля вам необходимо заново авторизоваться',
                        footer: '<a href="<?php echo $settings['link_to_admin'] ?>">Возникли вопросы?</a>'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.replace("/app/auth");
                        }
                    });
                    document.getElementById('submit').onclick = "";
                    document.getElementById('password').disabled = true;
                    document.getElementById('password_repeat').disabled = true;
                } else if (res.result == 2) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ошибка!',
                        text: 'Токен не найден',
                        footer: '<a href="<?php echo $settings['link_to_admin'] ?>">Возникли вопросы?</a>'
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
      