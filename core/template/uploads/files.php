<script src="/assets/js/jquery.uploadifive.min.js" type="text/javascript"></script>
<style>
    .uploadifive-button {
        background-color: #505050;
        background-image: linear-gradient(bottom, #505050 0%, #707070 100%);
        background-image: -o-linear-gradient(bottom, #505050 0%, #707070 100%);
        background-image: -moz-linear-gradient(bottom, #505050 0%, #707070 100%);
        background-image: -webkit-linear-gradient(bottom, #505050 0%, #707070 100%);
        background-image: -ms-linear-gradient(bottom, #505050 0%, #707070 100%);
        background-image: -webkit-gradient(
            linear,
            left bottom,
            left top,
            color-stop(0, #505050),
            color-stop(1, #707070)
        );
        background-position: center top;
        background-repeat: no-repeat;
        -webkit-border-radius: 30px;
        -moz-border-radius: 30px;
        border-radius: 30px;
        border: 2px solid #808080;
        color: #FFF;
        font: bold 12px Arial, Helvetica, sans-serif;
        text-align: center;
        text-shadow: 0 -1px 0 rgba(0,0,0,0.25);
        text-transform: uppercase;
        width: 100%;
    }
    .uploadifive-button:hover {
        background-color: #606060;
        background-image: linear-gradient(top, #606060 0%, #808080 100%);
        background-image: -o-linear-gradient(top, #606060 0%, #808080 100%);
        background-image: -moz-linear-gradient(top, #606060 0%, #808080 100%);
        background-image: -webkit-linear-gradient(top, #606060 0%, #808080 100%);
        background-image: -ms-linear-gradient(top, #606060 0%, #808080 100%);
        background-image: -webkit-gradient(
            linear,
            left bottom,
            left top,
            color-stop(0, #606060),
            color-stop(1, #808080)
        );
        background-position: center bottom;
    }
    .uploadifive-queue-item {
        background-color: #F5F5F5;
        border-bottom: 1px dotted #D5D5D5;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        font: 12px Arial, Helvetica, Sans-serif;
        margin-top: 3px;
        padding: 15px;
    }
    .uploadifive-queue-item .close {
        background: url('uploadifive-cancel.png') 0 0 no-repeat;
        display: block;
        float: right;
        height: 16px;
        text-indent: -9999px;
        width: 16px;
    }
    .uploadifive-queue-item .progress {
        border: 1px solid #D0D0D0;
        height: 3px;
        margin-top: 5px;
        width: 100%;
    }
    .uploadifive-queue-item .progress-bar {
        background-color: #0072BC;
        height: 3px;
        width: 0;
    }

    body {
        font: 13px Arial, Helvetica, Sans-serif;
    }
    .uploadifive-button {
        float: left;
        margin-right: 10px;
    }
    #queue {
        background: #fff;
        border: 1px solid #000;
        height: 177px;
        overflow: auto;
        margin-bottom: 10px;
        padding: 0 3px 3px;
        width: 300px;
    }
</style>
<div class="container">  
    <p class="page-title">Загрузка файлов</p>
    <div class="form">
        <form>
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
                        <option value="0" label="Не опубликовано">
                        <option value="1" label="Опубликовано">
                        <?php if($system->haveUserPermission($system_user_id, "EDIT_ALL_UPLOADS")) echo '
                            <option value="0" label="=============" disabled>
                            <option value="-1" label="Скрыть">';
                        ?>
                    </select><br>
                </div>
            </div>
            <div class="col-12">
                <div class="in">
                    <label for="queue" style="">Загружаемые файлы:</label><br>
		            <div id="queue"></div>
                    <input id="file_upload" name="file_upload" type="file" multiple="true">
                    <a class="uploadifive-button" style="position: relative;width: 100px;height: 30px;padding-top: 8px;" href="javascript:setForm();$('#file_upload').uploadifive('upload')">Загрузить</a>
                </div>
            </div>
        </form>
        <div class="col-12">
            <div class="in">
                <br><br><br><button type="submit" class="submit" onclick="submit();">Сохранить</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function submit() {
        Swal.fire({
            title: "Вы уверены?",
            text: "После сохранения вы не сможете изменять файлы загрузки",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#d33",
            confirmButtonText: "Да, сохранить!",
            cancelButtonText: "Отменить",
        }).then((result) => {
            if (result.isConfirmed) {
                save();
            }
        });
    }
    
    function save() {
        if(!document.getElementById('name').value || !document.getElementById('description').value)
            return Toast.fire({
                icon: 'error',
                title: 'Заполните, пожалуйста, все поля'
            });
        $.ajax({
            type: 'POST',
            url: '/api/uploads/edit',
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
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Произошла неизвестная ошибка!',
                    text: 'Обратитесь к администратору.',
                    footer: '<a href="<?php echo $settings['link_to_admin'] ?>">Возникли вопросы?</a>'
                });
                action = true;
            }
        }});
    }

    <?php $timestamp = time();?>
    function getCookie(name) {
        let matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }
    setForm();
    async function setForm() {
        await $('#file_upload').uploadifive({
            'auto'             : false,
            'checkScript'      : '/api/files/upload/check',
            'fileType'         : '.jpg,.jpeg,.gif,.png,.docx,.doc,.txt,.xls,.xlsx,.ppt,.pptx,.zip,.pdf',
            'queueID'          : 'queue',
            'buttonText'       : 'Выбрать файлы',
            'fileSizeLimit'    : 524288000,
            'simUploadLimit'   : 10,
            'formData'         : {
                                    'timestamp'  : '<?php echo $timestamp;?>',
                                    'token'      : '<?php echo md5('unique_salt' . $timestamp);?>',
                                    'id'         : '<?php echo $result['id'] ?>',
                                    'name'       : $("#name").val(),
                                    'description': $("#description").val(),
                                    'status'     : '0',
                                    'category'   : $("#category").val(),
                                },
            'uploadScript'     : '/api/files/upload',
            'onUploadComplete' : function(file, data) { console.log(data); }
        });
    }
</script>