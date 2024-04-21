<!DOCTYPE html>
<html>
<head>
<title>METANIT.COM</title>
<meta charset="utf-8" />
</head>
<body>
<?php
if($_FILES) {
    foreach ($_FILES["uploads"]["error"] as $key => $error) {
        if(count($_FILES["uploads"]["name"]) > 10) {
            echo "Количество файлов не должно превышать 10.";
            exit(-1);
        }
        if ($error == UPLOAD_ERR_OK) {
            $tmp_name = $_FILES["uploads"]["tmp_name"][$key];
            $name = $_FILES["uploads"]["name"][$key];
            if($_FILES["uploads"]["size"][$key] > 52428800) {
                echo "Ошибка! Вес файла ". $_FILES["uploads"]["name"][$key] ." не должен превышать 50 МБ";
                continue;
            }
            move_uploaded_file($tmp_name, "../../brigada-miit-storage/".$name);
        }
    }
    echo "Файлы загружены";
    print_r($_FILES);
}
?>
<h2>Загрузка файла</h2>
<form method="post" enctype="multipart/form-data">
    <input type="file" multiple="multiple" name="uploads[]" /><br />
    <input type="submit" value="Загрузить" />
</form>
</body>
</html>