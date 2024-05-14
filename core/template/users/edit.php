<style>
    .container {
        padding: 20px;
    }
    .form {
        display: flex;
        justify-content: space-between;
    }
    .form > div {
        width: 48%;
    }
    .form > div textarea {
        height: 100px;
    }
    table {
        max-width: 50%;
        border: 1px solid #ccc;
        border-collapse: collapse;
    }
    thead {
        border: 1px solid #ccc;
    }
    th, td {
        border-left: 1px solid #ccc;
        white-space: nowrap;
        padding: 5px;
    }
    td:nth-last-child(-n+2) {
        text-align: right;
    }
    .info p {
        padding-left: 20px;
        padding-top: 5px;
    }
    .info p:last-child {
        padding-bottom: 5px;
    }
    /*@media screen and (max-width: 729px) {
        .table-box {
            overflow-x: scroll;
        }
    }*/
</style>
<div class="container">
    <p class="page-title">Редактирование <a href="/profile/<?php echo $user['id'] ?>">пользователя</a> (ID: <?php echo $user['id'] ?>)</p>
    <div class="form">
        <div class="row">
            <div class="col-md-6">
                <div class="col-12">
                    <div class="in">
                        <label for="email">Email:</label><br>
                        <input id="email" type="text" disabled placeholder="Email" value="<?php echo $user['email'] ?>">
                    </div>
                </div>
                <div class="col-12">
                    <div class="in">
                        <label for="lastname">Фамилия</label><br>
                        <input id="lastname" type="text" placeholder="Фамилия" value="<?php echo $user['lastname'] ?>">
                    </div>
                </div>
                <div class="col-12">
                    <div class="in">
                        <label for="surname">Имя</label><br>
                        <input id="surname" type="text" placeholder="Имя" value="<?php echo $user['surname'] ?>">
                    </div>
                </div>
                <div class="col-12">
                    <div class="in">
                        <label for="patronymic">Отчество</label><br>
                        <input id="patronymic" type="text" placeholder="Отчество" value="<?php echo $user['patronymic'] ?>">
                    </div>
                </div>
                <div class="col-12">
                    <div class="in">
                    <!--  <input id="role" type="number" placeholder="Права" <?php echo $_user['user_type'] < 3 ? 'style="display:none;"' : ''?> value="<?php echo $user['user_type']?>"> -->
                    <?php if ($_user['user_type'] > 1): ?>
                    <label for="role">Роль:</label><br> 
                    <select id="role">
                        <option value="<?php echo $user['user_type']; ?>"><?php echo $system->getNameRole($user['user_type']); ?></option> 
                        <option>===========</option> 
                            <?php for ($type = 1; $type < $_user['user_type']; $type++): ?>
                                <?php if($type == $user['user_type']) continue; ?>
                                <option value="<?php echo $type; ?>"><?php echo $system->getNameRole($type); ?></option> 
                            <?php endfor; ?> 
                        </select> 
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-12">
                    <div class="in">
                        <label for="biography">О себе</label><br>
                        <textarea id="biography" placeholder="Введите информацию о себе..." style="width: 75%; display: block;"><?php echo $user['biography'] ?></textarea>
                    </div>
                </div>
                <div class="col-12">
                    <div class="in">
                        <label for="email_verifed">Email-адрес подтверждён?</label><br>
                        <input id="email_verifed" type="checkbox"<?php if($user['email_verifed'] != 0) echo ' checked disabled'; ?>>
                    </div>
                </div>
                <div class="col-12">
                    <div class="in">
                        <label for="ban_upload">Блокировка на загрузку файлов</label><br>
                        <input id="ban_upload" type="checkbox"<?php if($user['ban_upload'] != 0) echo ' checked'; ?>>
                    </div>
                </div>
                <div class="col-12">
                    <div class="in">
                        <label for="ban">Ограничение доступа к сайту</label><br>
                        <input id="ban" type="checkbox"<?php if($user['ban'] != 0) echo ' checked'; ?>>
                    </div>
                </div>
                <div class="col-12" style="margin-bottom: 20px;">
                    <div class="in">
                        <div class="btn-group d-flex flex-wrap">
                            <button id="submit" type="submit" class="submit mr-4 mb-2" onclick="edit();">Сохранить</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="in">
                    <h4 style="font-weight: bold;">Дополнительная информация</h4>
                    <div>
                        <div class="card border-dark mb-3 info">
                            <div class="card-header">Информация о пользователе</div>
                            <div>
                                <p><b>Дата регистрации:</b> <?php echo unixDateToString(intval($user['registred'])) ?></p>
                                <p><b>Подтверждение Email:</b> <?php echo ( ($user['email_verifed'] == 1) ? "Да" : "Нет" ) ?></p>
                                <p><b>Статус 2FA:</b> <?php echo ( !empty($user['2fa_secret']) ? "Включено" : "Выключено" ) ?></p>
                                <?php echo ( !empty($user['ban']) ? "<p><b>Блокировка:</b> Да (<a href='/profile/".$user['ban']."'>кем выдана?</a>)</p>" : "" ) ?>
                                <?php echo ( !empty($user['ban_upload']) ? "<p><b>Блокировка загрузки файлов:</b> Да (<a href='/profile/".$user['ban_upload']."'>кем выдана?</a>)</p>" : "" ) ?>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap">
                            <button class="btn btn-primary mb-2" style="margin-right: auto" onclick="submit_delete_avatar();">Удалить аватар</button>
                            <button class="btn btn-primary mb-2" style="margin-right: auto" onclick="submit_delete_2FA();">Удалить 2FA</button>
                            <button class="btn btn-danger  mb-2" onclick="submit_delete();">Удалить пользователя</button>
                        </div>
                    </div>
                    <hr>
                    <h4 style="font-weight: bold;">Отдельные права пользователя</h4>
                    <div id="perms" class="itable-box" style="overflow-x: scroll;">
                        <table style="max-width: 50%;">
                            <thead>
                                <tr>
                                    <?php
                                        $fields = array(); 
                                        $db = $system->db();
                                        $query = $db->query("SHOW COLUMNS FROM `permissions`");
                                        if ($query->num_rows > 0) {
                                            while ($row = $query->fetch_assoc()) {
                                                if($row['Field'] == "userid" || $row['Field'] == "id") {
                                                    continue;
                                                }
                                                if(!$system->haveUserPermission($system->userinfo()['id'], $row['Field'])) {
                                                    continue;
                                                }
                                                echo '<th>' . $row['Field'] . '</th>';
                                                $fields[] = $row['Field'];
                                            }
                                        }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <?php
                                    $query_p = $db->query("SELECT * FROM `permissions` WHERE `userid`=".$user['id'].";");
                                    $result = $query_p->fetch_assoc();
                                    //print_r($result);

                                    if($result) {
                                        foreach ($fields as $field) {
                                            if($result[$field])
                                                echo '<td><input id="'.$field.'" style="width: 100%;height: 25px;box-shadow: none;" type="checkbox" checked></td>';
                                            else if($system->haveGroupPermissions($system->userinfo($user['id'])['user_type'], $field))
                                                echo '<td><input id="'.$field.'" style="width: 100%;height: 25px;box-shadow: none;" type="checkbox" checked disabled></td>';
                                            else
                                                echo '<td><input id="'.$field.'" style="width: 100%;height: 25px;box-shadow: none;" type="checkbox"></td>';
                                        }
                                    }
                                ?>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <br><button id="submit" type="submit" class="btn btn-primary" onclick="updatePermissions();">Сохранить права</button>
                </div>
            </div>
            <p class="result"></p>
        </div>
    </div>
</div>
<script>
    function updatePermissions() {
        let result = document.querySelector('.result');
        let xhr = new XMLHttpRequest();
        let url = "/api/users/permissions";

        xhr.open("POST", url, true);
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                function notify(data) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: true,
                        timer: 5500,
                        timerProgressBar: true
                    });
                    if (data.result == 1) {
                        Toast.fire({
                            icon: 'success',
                            title: data.text,
                        });
                    }
                    else {
                        Toast.fire({
                            icon: 'error',
                            title: data.text
                        });
                    }
                }
                notify(JSON.parse(this.responseText));
            }
        };

        var value = new Map();
        value.set("id", <?php echo $user['id']?>)
        $("#perms :checkbox").each(function(){
            if(!this.disabled)
                value.set(this.id, this.checked);
        });

        var data = JSON.stringify(Array.from(value.entries()));
        console.log(data);
        xhr.send(data);
    }

    function edit() {
        $.ajax({
            type: 'post',
            url: "/api/users/edit",
            data: 'id=<?php echo $user['id']?>&role='+$("#role").val()+'&lastname='+$("#lastname").val()+'&surname='+$("#surname").val()+'&patronymic='+$("#patronymic").val()+'&biography='+$("#biography").val()+'&ban_upload='+(($('#ban_upload').is(":checked")) ? '1' : '0')+'&ban='+(($('#ban').is(":checked")) ? '1' : '0')+'&email_verifed='+(($('#email_verifed').is(":checked")) ? '1' : '0'),
            dataType: 'json',
            success: function(data){
                console.log(data);
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: true,
                    timer: 5500,
                    timerProgressBar: true
                });
                if (data.result == 1) {
                    Toast.fire({
                        icon: 'success',
                        title: data.text,
                    }).then((result) => {
                        if (result.isConfirmed) {
                           return location.reload(); 
                        }
                    });

                    document.getElementById('submit').onclick = "";
                    document.getElementById('role').disabled = true;
                    document.getElementById('lastname').disabled = true;
                    document.getElementById('surname').disabled = true;
                    document.getElementById('patronymic').disabled = true;
                    document.getElementById('biography').disabled = true;
                    document.getElementById('email_verifed').disabled = true;
                    document.getElementById('ban_upload').disabled = true;
                    document.getElementById('ban').disabled = true;

                    function reload() {
                        return location.reload(); 
                    }

                    setTimeout(reload, 5575);
                }
                else if(data.result == 4) {
                    Toast.fire({
                        icon: 'info',
                        title: data.text
                    });
                }
                else {
                    Toast.fire({
                        icon: 'error',
                        title: data.text
                    });
                }
            }
        });
        //updatePermissions();
    }

    function submit_delete() {
        Swal.fire({
            title: "Вы уверены?",
            text: "После удаления восстановление пользователя будет невозможно",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#d33",
            confirmButtonText: "Да, удалить!",
            cancelButtonText: "Отменить",
        }).then((result) => {
            if (result.isConfirmed) {
                delete_user();
            }
        });
    }

    function delete_user() {
        $.ajax({
            type: 'POST',
            url: '/api/users/delete',
            data: 'id=<?php echo $args['id'] ?>',
            success: async function(data) {
                var res = $.parseJSON(data);
                console.log(res);
                if (res.result == 1) {
                    Swal.fire({
                        title: "Успешно!",
                        text: res.text,
                        icon: "success"
                    }).then((result) => {
                        location.replace("/app/users");
                    });
                }
                else if (res.result == 0) {
                    Swal.fire({
                        title: "Ошибка!",
                        text: res.text,
                        icon: "error"
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

    function submit_delete_avatar() {
        Swal.fire({
            title: "Вы уверены?",
            text: "После удаления аватара его восстановление будет невозможно",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#d33",
            confirmButtonText: "Да, удалить!",
            cancelButtonText: "Отменить",
        }).then((result) => {
            if (result.isConfirmed) {
                delete_avatar();
            }
        });
    }

    function delete_avatar() {
        $.ajax({
            type: 'POST',
            url: '/api/profile/avatar/delete/<?php echo $user['id'] ?>',
            success: async function(data) {
                var res = $.parseJSON(data);
                console.log(res);
                if (res.result == 1) {
                    Swal.fire({
                        title: "Успешно!",
                        text: "Аватар пользователя был успешно удалён",
                        icon: "success"
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

    function submit_delete_2FA() {
        Swal.fire({
            title: "Вы уверены?",
            text: "Вы хотите удалить двухфакторную авторизацию у пользователя?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#d33",
            confirmButtonText: "Да, удалить!",
            cancelButtonText: "Отменить",
        }).then((result) => {
            if (result.isConfirmed) {
                delete_2FA();
            }
        });
    }

    function delete_2FA() {
        $.ajax({
            type: 'POST',
            url: '/api/users/2fa_delete/<?php echo $user['id'] ?>',
            success: async function(data) {
                var res = $.parseJSON(data);
                console.log(res);
                if (res.result == 1) {
                    Swal.fire({
                        title: "Успешно!",
                        text: "Двухфакторная авторизация у пользователя была успешно удалена",
                        icon: "success"
                    }).then((result) => {
                        location.reload();
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
</script>
