
<?php
require '../core/vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('error_reporting',  E_ALL); 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer;
$mail->setFrom('noreply@brigada-miit.ru', 'MyNameCompany');
$mail->addAddress('otghikogh@ya.ru', ''); // куда отправить письмо, укажите нужный email
$mail->CharSet = 'UTF-8';
$mail->Subject  ='Тестовое письмо с dkim подписью';
$mail->Body     = 'Текст нашего письма без HTML разметки'; // письмо без html 
$mail->IsHTML(true);
$mail->msgHTML('<strong>Текст нашего письма с HTML разметкой</strong>' ); // письмо с html

$mail->DKIM_domain = 'brigada-miit.ru';
$mail->DKIM_private = $_SERVER['DOCUMENT_ROOT'].'/../core/vendor/dkim_private.pem';
$mail->DKIM_selector = 'mail';
$mail->DKIM_identity = $mail->From;

if ($mail->send()) {
 echo 'Письмо успешно отправлено!';
} else {
 echo 'Ошибка: '. $mail->ErrorInfo;
}
?>