<div class="container">  
    <p class="page-title">Создание новой страницы</p>
    <h3>После создания страницы вам будет предложено загрузить файлы<br>ВНИМАНИЕ! Файлы можно загрузить только один раз. После загрузки изменять можно будет только: название, описание, категорию.</h3>
    <div class="form">
        <div class="col-12">
            <div class="in">
                <label for="name">Название загрузки</label><br>
                <input type="text" class="text" id="name" placeholder="Введите название...">
            </div>
        </div>
        <div class="col-12">
            <div class="in">
                <label for="description">Описание загрузки</label><br>
                <textarea id="description" required placeholder="Введите описание загрузке..." style="width: 75%; display: block;"></textarea>
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
                            $result = $query->fetch_assoc();
                            echo "<option value='".$result['id']."' label='".$result['name']."'>";
                        }
                    ?>
                </select><br>
            </div>
        </div>
        <div class="col-12">
            <div class="in">
                <br><button id="submit" type="submit" class="submit" onclick="submit();">Создать</button>
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
            url: '/api/uploads/create',
            data: 'name='+document.getElementById('name').value+'&description='+document.getElementById('description').value+'&category='+document.getElementById('category').value,
            success: async function(data) {
            var res = $.parseJSON(data);
            console.log(res);
            if (res.result == 1) {
                document.getElementById('submit').onclick = "";
                document.getElementById('name').disabled = true;
                document.getElementById('description').disabled = true;
                document.getElementById('category').disabled = true;
                location.replace("/uploads/files/"+res.text);
            } else if (res.result == 2) {
                Swal.fire({
                    icon: 'error',
                    title: 'Ошибка!',
                    text: res.text,
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
        }});
    }
</script>   
      