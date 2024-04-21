<!DOCTYPE html>
<html>
<head>
<title>METANIT.COM</title>
<meta charset="utf-8" />
</head>
<body>
<?php
?>
<h2>Загрузка файла</h2>
<form method="post" action="/api/files/upload" enctype="multipart/form-data">
    <input type="file" name="uploads[]" /><br />
    <input type="file" name="uploads[]" /><br />
    <input type="file" name="uploads[]" /><br />
    <input type="submit" value="Загрузить" />
</form>
</body>
</html>