<?php
namespace Bidding\Proposals;
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require '../../../../vendor/autoload.php';
require '../../../config/server.php';
require '../../../config/constants/mail.php';

class Mailer {
    public function __construct() {
        $this->mail = new PHPMailer(true);  
    }  

    public function send($message, $to = [], $bcc= []) {
        try {

            # O365 Server settings
            /*$this->mail->SMTPDebug = 0;                                 // Enable verbose debug output
            $this->mail->isSMTP();                                      // Set mailer to use SMTP
            $this->mail->Host = 'smtp.office365.com';                   // Specify main and backup SMTP servers
            $this->mail->SMTPAuth = true;                               // Enable SMTP authentication
            $this->mail->Username = SMTP_USERNAME;                      // SMTP username
            $this->mail->Password = SMTP_PASSWORD;                      // SMTP password
            $this->mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $this->mail->Port = 587;                                    // TCP port to connect to

            //Recipients
            $this->mail->setFrom(SMTP_USERNAME, SMTP_SENDER);*/


            # 3rd party server settings
            $this->mail->SMTPDebug = 0;                             // Enable verbose debug output
            // $this->mail->isSMTP();                                  // Set mailer to use SMTP
            $this->mail->Host = 'localhost';                        // Specify main and backup SMTP servers
            $this->mail->Port = 25;                                 // TCP port to connect to


            # Recipients
            $this->mail->setFrom(SMTP_USERNAME, SMTP_SENDER);

            if (count($to) > 0) {
                $receivers = $to;
            } else {
                $receivers = PROPOSAL_MAIL_RECEIVERS; 
            }

            # TO
            foreach ($receivers as $key => $value) {
                $this->mail->addAddress($value, $value);     // Add a recipient
            }


            # BCC
            foreach ($bcc as $key => $value) {
                $this->mail->addBCC($value, $value);
            }
            


            #  Content
            $this->mail->isHTML(true);                                  // Set email format to HTML
            $this->mail->Subject = 'Supplier submited a new proposal';
            $this->mail->Body    = $message;

            # convert to non HTML message
            # $this->mail->AltBody = strip_tags($message);
            $this->mail->send();
           return true;
        } catch (Exception $e) {
            return false;
           # echo 'Message could not be sent. Mailer Error: ', $this->mail->ErrorInfo;
        }    
    }
}



?>