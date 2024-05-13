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
                <label for="lastname">Фамилия</label><br>
                <input id="lastname" type="text" placeholder="Фамилия" value="<?php echo $_user['lastname'] ?>">
            </div>
        </div>
        <div class="col-12">
            <div class="in">
                <label for="surname">Имя</label><br>
                <input id="surname" type="text" placeholder="Имя" value="<?php echo $_user['surname'] ?>">
            </div>
        </div>
        <div class="col-12">
            <div class="in">
                <label for="patronymic">Отчество</label><br>
                <input id="patronymic" type="text" placeholder="Отчество" value="<?php echo $_user['patronymic'] ?>">
            </div>
        </div>
        <div class="col-12">
            <div class="in">
                <label for="biography">О себе</label><br>
                <textarea id="biography" placeholder="Введите информацию о себе..." style="width: 75%; display: block;"><?php echo $_user['biography'] ?></textarea>
            </div>
        </div>
        <div class="col-12" style="margin-top: 5%;"><br>
            <div class="in">
                <div class="btn-group d-flex flex-wrap">
                    <button id="submit" type="submit" class="submit mr-4 mb-2" onclick="edit();">Сохранить</button>
                    <button id="submit" type="submit" class="submit mr-4 mb-2" onclick="password();">Сменить пароль</button>
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
            data: 'lastname='+$("#lastname").val()+'&surname='+$("#surname").val()+'&patronymic='+$("#patronymic").val()+'&ban_upload='+(($('#ban_upload').is(":checked")) ? '1' : '0')+'&ban='+(($('#ban').is(":checked")) ? '1' : '0')+'&email_verifed='+(($('#email_verifed').is(":checked")) ? '1' : '0'),
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
    }
</script>
