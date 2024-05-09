<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            Информация о загрузке
        </div>
        <div class="card-body">
            <h5 class="card-title"><b>Название:</b> <?php echo $result['name'] ?></h5>
            <p class="card-text"><b>Описание:</b> <?php echo $result['description'] ?></p>
            <p class="card-text"><b>Автор:</b> <?php echo (empty($result_author['lastname']) ? "Пользователь удалён" : "<a href='/profile/".$result_author['id']."'>".($result_author['surname'])." ".$result_author['lastname'])."</a>" ?></p>
            <p class="card-text"><b>Дата:</b> <?php echo unixDateToString(intval($result['created'])) ?></p>
            <p class="card-text"><b>Статус:</b> <?php echo ($result['status'] != -1 ? (($result['status'] == 1) ? "Опубликован" : "Не опубликован") : "Скрыт") ?></p>
            <p class="card-text"><b>Категория:</b> <?php echo $result_category['name']; ?></p>
            <hr>
            <h5 class="card-title">Файлы:</h5>
            <ul class="list-group">
                {{#post.files}}
                <li class="list-group-item">
                    <a href="/uploads/files/download/{{id}}" class="btn btn-link">
                        <!-- Вставьте здесь логотип обозначения файла -->
                        {{file_name}}
                    </a>
                </li>
                {{/post.files}}
            </ul>
        </div>
    </div>
</div>

      