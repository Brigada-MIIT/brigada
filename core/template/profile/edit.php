<div class="container">
    <p class="page-title">Редактирование профиля</p>
    <div class="form">
        <div class="col-12">
            <div class="in">
                <label for="email">Email:</label><br>
                <input id="email" type="text" disabled placeholder="Email" value="<?php echo $_user['email'] ?>">
            </div>
        </div>
        <div class="col-12">
            <div class="in">
                <label for="lastname">Фамилия:</label><br>
                <input id="lastname" type="text" placeholder="Фамилия" value="<?php echo $_user['lastname'] ?>">
            </div>
        </div>
        <div class="col-12">
            <div class="in">
                <label for="surname">Имя:</label><br>
                <input id="surname" type="text" placeholder="Имя" value="<?php echo $_user['surname'] ?>">
            </div>
        </div>
        <div class="col-12">
            <div class="in">
                <label for="patronymic">Отчество:</label><br>
                <input id="patronymic" type="text" placeholder="Отчество" value="<?php echo $_user['patronymic'] ?>">
            </div>
        </div>
        <div class="col-12">
            <div class="in">
                <label for="biography">О себе:</label><br>
                <textarea id="biography" placeholder="Введите информацию о себе..." style="width: 75%; display: block;"><?php echo $_user['biography'] ?></textarea>
            </div>
        </div>
        <div class="col-12" style="margin-top: 5%;">
            <div class="in">
                <div class="btn-group d-flex flex-wrap">
                    <button id="submit" type="submit" class="submit mr-4 mb-2" onclick="edit();">Сохранить</button>
                    <a href="/profile/edit/avatar"><button id="submit" type="submit" class="submit mr-4 mb-2">Сменить аватар</button></a>
                    <button id="submit" type="submit" class="submit mr-4 mb-2" onclick="submit_change_password();">Сменить пароль</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function edit() {
        $.ajax({
            type: 'post',
            url: "/api/profile/edit",
            data: 'lastname='+$("#lastname").val().trim()+'&surname='+$("#surname").val().trim()+'&patronymic='+$("#patronymic").val().trim()+'&biography='+$("#biography").val().trim(),
            dataType: 'json',
            success: function(data){
                console.log(data);
                if (data.result == 1) {
                    Swal.fire({
                        title: "Успешно!",
                        text: "Ваш профиль был изменён",
                        icon: "success"
                    }).then((result) => {
                        if (result.isConfirmed) {
                           return location.reload(); 
                        }
                    });

                    document.getElementById('submit').onclick = "";
                    document.getElementById('lastname').disabled = true;
                    document.getElementById('surname').disabled = true;
                    document.getElementById('patronymic').disabled = true;
                    document.getElementById('biography').disabled = true;

                    function reload() {
                        return location.reload(); 
                    }

                    setTimeout(reload, 5575);
                }
                else if (data.result == 0) {
                    Swal.fire({
                        title: "Ошибка!",
                        text: data.text,
                        icon: "error",
                        footer: '<a href="<?php echo $settings['link_to_admin'] ?>">Возникли вопросы?</a>'
                    });
                }
                else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Произошла неизвестная ошибка!',
                        text: 'Обратитесь к администратору.',
                        footer: '<a href="<?php echo $settings['link_to_admin'] ?>">Возникли вопросы?</a>'
                    });
                }
            }
        });
    }

    function submit_change_password() {
        Swal.fire({
            title: "Вы хотите сменить пароль?",
            text: "На вашу электронную почту будет выслана ссылка для смены пароля",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#d33",
            confirmButtonText: "Да, хочу сменить!",
            cancelButtonText: "Отменить",
        }).then((result) => {
            if (result.isConfirmed) {
                change_password();
            }
        });
    }

    function change_password() {
        $.ajax({
            type: 'POST',
            url: '/api/profile/change_password',
            success: async function(data) {
                var res = $.parseJSON(data);
                console.log(res);
                if (res.result == 1) {
                    Swal.fire({
                        title: "Успешно!",
                        text: "На вашу электронную почту была выслана инструкция по смене пароля. Если письмо не пришло, пожалуйста, проверьте папку «Спам»",
                        icon: "success"
                    })
                }
                else if (res.result == 2) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ошибка!',
                        text: 'Вы слишком часто пытаетесь сменить пароль',
                        footer: '<a href="<?php echo $settings['link_to_admin'] ?>">Возникли вопросы?</a>'
                    })
                }
                else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Произошла неизвестная ошибка!',
                        text: 'Обратитесь к администратору.',
                        footer: '<a href="<?php echo $settings['link_to_admin'] ?>">Возникли вопросы?</a>'
                    });
                }
            }
        });
    }
</script>
