<?php
    namespace MF\Controller;

    require_once "PHPMailer/src/PHPMailer.php";
    require_once "PHPMailer/src/SMTP.php";
    require_once "PHPMailer/src/Exception.php";

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    abstract class Emails extends Action{
        protected $mail;
        public function __construct(){
            $this->view = new \stdClass();

            $this->mail = new PHPMailer(false);
            
            $this->mail->isSMTP();
            $this->mail->Host = 'smtp.gmail.com';
            $this->mail->SMTPAuth = true;
            $this->mail->Username = 'psnmalasiamarceloht11@gmail.com';
            $this->mail->Password = 'Marcelo@01';
            $this->mail->Port = 587;
            $this->mail->setFrom('psnmalasiamarceloht11@gmail.com');
        }
    }
?>
