<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>UploadiFive Test</title>
<script src="/assets/js/jquery.min.js" type="text/javascript"></script>
<script src="/assets/js/sweetalert2.js" type="text/javascript"></script>
<style type="text/css">
body {
	font: 13px Arial, Helvetica, Sans-serif;
}
</style>
</head>
<body>
	<h1>Создание новой страницы</h1>
    <h2>После создания страницы вам будет предложено загрузить файлы<br>ВНИМАНИЕ! Файлы можно загрузить только один раз. После загрузки изменять можно будет только: название, описание, категорию.</h2>
	<form>
        <label for="name">Название загрузки</label>  
        <input id="name" type="text" required placeholder="Введите название загрузке..." style="height: 5%; width: 30%; display: block;"><br>
        <label for="description">Описание загрузки</label>
        <textarea id="description" required placeholder="Введите описание загрузке..." style="width: 75%; display: block;"></textarea><br>
        <label for="description">Категория загрузки</label>
        <select id="category">
			<option value="1" label="Test" >
		</select><br>
        <button onclick="submit();">Создать</button>
	</form>
	<script>
        let action = true;

        function submit() {
            if(!document.getElementById('name').value || !document.getElementById('description').value) return;
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
                    location.replace("/uploads/files/"+res.text);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Произошла неизвестная ошибка!',
                        text: 'Обратитесь к администратору.',
                        footer: '<a href="<?php echo $link_to_admin ?>">Возникли вопросы?</a>'
                    });
                    action = true;
                }
            }});
        }
	</script>
</body>
</html>