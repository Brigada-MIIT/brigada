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
            <h5 class="card-title"><b>Файлы:</b></h5>
            <ul class="list-group">
                <li class="list-group-item">
                    <a href="/uploads/files/download/{{id}}" class="btn btn-link d-flex justify-content-between align-items-center">
                        <div>
                            <!--<img src="microsoft_word_logo.png" alt="Microsoft Word" width="16" height="16" class="mr-1">-->
                            {{file_name}}
                        </div>
                        <span class="ml-auto">{{file_size}}</span>
                    </a>
                </li>
                <li class="list-group-item">
                    <a href="/uploads/files/download/{{id}}" class="btn btn-link d-flex justify-content-between align-items-center">
                        <div>
                            <!--<img src="microsoft_word_logo.png" alt="Microsoft Word" width="16" height="16" class="mr-1">-->
                            {{file_name}}
                        </div>
                        <span class="ml-auto">{{file_size}}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

      