<div class="container">
    <p class="page-title">Настройки</p>
    <div class="form">
        <div class="col-12">
            <div class="in">
                <label for="max_size_avatar">Максимальный размер загрузки аватарки (МБ):</label><br>
                <input id="max_size_avatar" type="number" value="<?php echo $settings['max_size_avatar']?>"><br>
            </div>
        </div>
        <div class="col-12">
            <div class="in">
                <label for="max_size_file">Максимальный размер загрузки файла (МБ):</label><br>
                <input id="max_size_file" type="number" value="<?php echo $settings['max_size_file']?>"><br>
            </div>
        </div>
        <div class="col-12">
            <div class="in">
                <label for="link_to_admin">Ссылка на администратора:</label><br>
                <input id="link_to_admin" type="text" value="<?php echo $settings['link_to_admin']?>"><br>
            </div>
        </div>
        <div class="col-12">
            <div class="in">
                <label for="count_char_uploads_name">Количество символов в названии загрузки:</label><br>
                <input id="count_char_uploads_name" type="text" value="<?php echo $settings['count_char_uploads_name']?>"><br>
            </div>
        </div>
        <div class="col-12">
            <div class="in">
                <label for="count_char_uploads_description">Количество символов в описании загрузки:</label><br>
                <input id="count_char_uploads_description" type="text" value="<?php echo $settings['count_char_uploads_description']?>"><br>
            </div>
        </div>
        <div class="col-12">
            <div class="in">
                <br><button type="submit" class="submit" onclick="save();">Сохранить</button>
            </div>
        </div>
    </div>
</div>
<script>
    function save() {
        $.ajax({
            type: 'post',
            url: "/api/settings/update",
            data: '&max_size_avatar='+$("#max_size_avatar").val()+'&link_to_admin='+$("#link_to_admin").val()+'&max_size_file='+$("#max_size_file").val()+'&count_char_uploads_name='+$("#count_char_uploads_name").val()+'&count_char_uploads_description='+$("#count_char_uploads_description").val(),
            dataType: 'json',
            success: function(data){
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: true,
                    timer: 6100,
                    timerProgressBar: true
                });
                if (data.result == 1) {
                    Toast.fire({
                        icon: 'success',
                        title: data.text,
                    }).then((result) => {
                        if (result.isConfirmed) {
                           return location.replace("/app/settings"); 
                        }
                    });

                    function reload() {
                        return location.replace("/app/settings");
                    }

                    setTimeout(reload, 6075);
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