<div class="container">
    <p class="page-title">Изменение аватарки</p>
    <div class="form">
        <div class="col-12">
            <div class="in">
                <form method="POST" action="/api/profile/avatar" enctype="multipart/form-data">
                    <br><input type="file" name="avatar"><br><br>
                    <div class="col-12" style="margin-top: 5%;">
                        <div class="in">
                            <div class="btn-group d-flex flex-wrap">
                                <button type="submit" class="submit mr-4 mb-2">Сохранить</button>
                                <button type="button" class="submit mr-4 mb-2" onclick="submit_delete();">Удалить аватар</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function submit_delete() {
        Swal.fire({
            title: "Вы уверены?",
            text: "После удаленияа аватара его восстановление будет невозможно",
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
            url: '/api/profile/avatar/delete',
            success: async function(data) {
                var res = $.parseJSON(data);
                console.log(res);
                if (res.result == 1) {
                    Swal.fire({
                        title: "Успешно!",
                        text: "Ваш аватар был успешно удалён",
                        icon: "success"
                    }).then((result) => {
                        location.replace("/profile/<?php echo $system_user_id ?>");
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