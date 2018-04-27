<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
/*use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require '../../../../vendor/autoload.php';
require '../../../config/server.php';
require '../../../config/constants/mail.php';

$mail = new PHPMailer(true); 

try {
    //Server settings
    $mail->SMTPDebug = 0;                                 // Enable verbose debug output
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.office365.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = SMTP_USERNAME;                 // SMTP username
    $mail->Password = SMTP_PASSWORD;                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to

    //Recipients
    $mail->setFrom(SMTP_USERNAME, SMTP_SENDER);
    $mail->addAddress('johnkennethgibasabella@gmail.com', 'John');     // Add a recipient


    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    return true;
} catch (Exception $e) {
	return false;
    # echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
}*/

?>