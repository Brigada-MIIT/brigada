<div class="container">  
    <p class="page-title">Редактирование загрузки<?php if($result['author'] != $system_user_id) echo " (<a href='/profile/".$result['author']."'>перейти к автору</a>)" ?></p>
    <div class="form">
        <div class="col-12">
            <div class="in">
                <label for="name">Название загрузки</label><br>
                <input id="name" type="text" value="<?php echo $result['name'] ?>" placeholder="Введите название загрузке...">
            </div>
        </div>
        <div class="col-12">
            <div class="in">
                <label for="description">Описание загрузки</label>
                <textarea id="description" placeholder="Введите описание загрузке..." style="width: 75%; display: block;"><?php echo $result['description'] ?></textarea>
            </div>
        </div>
        <div class="col-12">
            <div class="in">
                <label for="category">Категория загрузки</label><br>
                <select id="category">
                    <?php 
                        $query = $db->query("SELECT * FROM `categories` WHERE `status` = 1");
                        if(!$query || $query->num_rows == 0)
                            die("Categories error");
                        for($i = 0; $i < $query->num_rows; $i++) {
                            $results = $query->fetch_assoc();
                            echo "<option value='".$results['id']."' label='".$results['name']."'".(($result['category'] == $results['id']) ? ' selected' : '').">";
                        }
                    ?>
                </select><br>
            </div>
        </div>
        <div class="col-12">
            <div class="in">
                <label for="status">Статус загрузки</label><br>
                <select id="status">
                    <option value="0" label="Не опубликовано"<?php if($result['status'] == 0) echo ' selected' ?>>
                    <option value="1" label="Опубликовано"<?php if($result['status'] == 1) echo ' selected' ?>>
                    <?php if($system->haveUserPermission($system_user_id, "EDIT_ALL_UPLOADS")) echo '
                        <option value="0" label="==== MODERATION ====" disabled>
                        <option value="-1" label="Скрыть"'.(($result['status'] == -1) ? " selected" : "").'>';
                    ?>
                </select><br>
            </div>
        </div>
        <div class="col-12" style="margin-top: 5%;">
            <div class="in">
                <div class="btn-group d-flex flex-wrap">
                    <button type="submit" class="submit mr-4 mb-2" onclick="save();">Сохранить</button>
                    <?php if($result['author'] == $system_user_id || $system->haveUserPermission($system_user_id, "DELETE_ALL_UPLOADS")) echo '<button type="submit" class="submit mb-2" onclick="submit_delete();">Удалить загрузку</button>' ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function save() {
        if(!document.getElementById('name').value || !document.getElementById('description').value)
            return Toast.fire({
                icon: 'error',
                title: 'Заполните, пожалуйста, все поля'
            });
        $.ajax({
            type: 'POST',
            url: '/api/uploads/edit/<?php echo $args['id'] ?>',
            data: 'name='+document.getElementById('name').value+'&description='+document.getElementById('description').value+'&category='+document.getElementById('category').value+'&status='+document.getElementById('status').value,
            success: async function(data) {
                var res = $.parseJSON(data);
                console.log(res);
                if (res.result == 1) {
                    Swal.fire({
                        title: "Успешно!",
                        text: "Ваша загрузка была изменена",
                        icon: "success"
                    }).then((result) => {
                        location.replace("/uploads/view/"+res.text);
                    });
                }
                else if (res.result == 2) {
                    Swal.fire({
                        title: "Ошибка!",
                        text: "Вы не можете сохранить пока не загрузите файлы",
                        icon: "error"
                    });
                } else {
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

    function submit_delete() {
        Swal.fire({
            title: "Вы уверены?",
            text: "После удаления восстановление файлов будет невозможно",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#d33",
            confirmButtonText: "Да, удалить!",
            cancelButtonText: "Отменить",
        }).then((result) => {
            if (result.isConfirmed) {
                delete_upload();
            }
        });
    }

    function delete_upload() {
        $.ajax({
            type: 'POST',
            url: '/api/uploads/delete/<?php echo $args['id'] ?>',
            success: async function(data) {
                var res = $.parseJSON(data);
                console.log(res);
                if (res.result == 1) {
                    Swal.fire({
                        title: "Успешно!",
                        text: "Ваша загрузка была удалена",
                        icon: "success"
                    }).then((result) => {
                        location.replace("/");
                    });
                }
                else if (res.result == 3) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ошибка!',
                        text: res.text,
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
</script>