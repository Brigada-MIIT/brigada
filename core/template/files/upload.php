<html>
<header>
    <style>
        .file-drop {
            background:#fff;
            margin:auto;
            padding:200px 200px;
            border:2px solid #333;
        }
        .file-drop_dragover {
            border:2px dashed #333;
        }
        .file-drop__input {
            border:0;
        }
        .message-div {
            background:#fefefe;
            border:2px solid #333;
            color:#333;
            width:350px;
            height:150px;
            position:fixed;
            bottom:25px;
            right:25px;
            font-size:15px;
            padding:5px;
            z-index:99999;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }
        .message-div_hidden {
            right:-9999999999999999999px;
        }
    </style>
</header>
<body>
    <form method='post' action="/file.php" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="5000000">
        <input type='file' name='file[]' class='file-drop' id='file-drop' multiple required><br>
        <input type='submit' value='Загрузить' >
    </form>
    <div class='message-div message-div_hidden' id='message-div'></div>
</body>
<script>
    var fileDrop = document.getElementById('file-drop'); //Получаем объекты
    var loadButton = document.getElementById('load-button');
    var messageDiv = document.getElementById("message-div");

    function dragOver() {//Меняем рамку, когда пользователь перетаскивает файл на поле
        fileDrop.setAttribute('class','file-drop file-drop_dragover');
    }

    function dragOut() {//Возвращаем обычную рамку
        fileDrop.setAttribute('class','file-drop');
    }

    function closeMessage() {//Закрываем сообщение
        messageDiv.setAttribute('class','message-div message-div_hidden');
        messageDiv.innerHTML = '';
    }

    function showMessage(data = 0) {//Показываем сообщение о том, что началась или закончилась загрузка
        if(data == 0) {
            data = "Загрузка...";
        } 
        else {
            setTimeout(closeMessage,4000);
        }
        messageDiv.innerHTML = data;
        messageDiv.setAttribute('class','message-div');
    }

    function uploadFile() { //Загружаем файл
        dragOut();
        var files = this.files;
        var data = new FormData();
        for(var i = 0; i < files.length; i++) { //Помещаем в дата массив с файлами
            data.append(i, files[i]);
        }
        showMessage(); //Показываем сообщение, что загрузка началась
        $.ajax({
            url: "/api/files/upload", //Ссылка на обработчик
            type: 'POST', //Метод передачи
            data: data, //Массив с файлами
            cache: false, //Обязательно указать false
            processData: false, //Обязательно указать false
            contentType: false, // Обязательно указать false
            success: function (data) {//В случае успеха показываем сообщение с результатом работы и очищаем поле
                showMessage(data);
                fileDrop.value = null;
            }
        });
    }
    //Указываем события для вызова функций
    fileDrop.addEventListener('dragover',dragOver);
    fileDrop.addEventListener('dragleave',dragOut);
    fileDrop.addEventListener('change',uploadFile); //Отправляем файлы сразу после того, как они будут выбраны
</script>
</html>