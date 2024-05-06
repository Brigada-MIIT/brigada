<div class="container">
    <style>
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
        /*@media screen and (max-width: 729px) {
            .table-box {
                overflow-x: scroll;
            }
        }*/
    </style>
    <p class="page-title">Редактирование пользователя (ID: <?php echo $user['id'] ?>)</p>
    <div class="form">
        <div class="row" style="width: 100%;">
            <div style="float:left; width: 50%">
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
                        <label for="verifed_email">Email-адрес подтверждён?</label><br>
                        <input id="verifed_email" type="checkbox"<?php if($user['email_verfied'] == 1) echo ' checked disabled'; ?>>
                    </div>
                </div>
                <div class="col-12">
                    <div class="in">
                        <label for="ban_upload">Блокировка на загрузку файлов</label><br>
                        <input id="ban_upload" type="checkbox"<?php if($user['ban_upload'] == 1) echo ' checked'; ?>>
                    </div>
                </div>
                <div class="col-12">
                    <div class="in">
                        <label for="ban">Ограничение доступа к сайту</label><br>
                        <input id="ban" type="checkbox"<?php if($user['ban'] == 1) echo ' checked'; ?>>
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
                <div class="col-12"><br><br>
                    <button id="submit" type="submit" class="submit" onclick="edit();">Сохранить</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="/users/delete/<?php echo $user['id'] ?>"><button id="submit" type="submit" class="submit">Удалить пользователя</button></a>
                </div>
            </div>
            <div style="float:right; width: 50%;">
                <div class="in">
                    <p style="font-size: larger;font-weight: bold;">Отдельные права пользователя</p>
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
                    <br><button id="submit" type="submit" class="submit" onclick="updatePermissions();">Сохранить права</button>
                </div>
            </div>
        </div>
        <p class="result"></p>
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
            data: 'id=<?php echo $user['id']?>&role='+$("#role").val(),
            dataType: 'json',
            success: function(data){
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
                    document.getElementById('password').disabled = true;
                    document.getElementById('role').disabled = true;

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
</script>
