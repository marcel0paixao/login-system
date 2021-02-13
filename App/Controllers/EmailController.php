<?php
    namespace App\Controllers;
    use MF\Controller\Action;
    use MF\Model\Container;
    use MF\Controller\Emails;
    use App\Controllers\AuthController;

    /*
    * class EmailController, responsible for define functions to control email actions.
    * EVERY controller class have to extend the Action class to render solicited webpages
    */
    class EmailController extends Emails{
        private $hash;
        private $pass;
        private $oldPass;
        private $requestType;

        public function __get($var){
            return $this->$var;
        }
        public function __set($var, $value){
            return $this->$var = $value;
        }

        //function that uses the PHPMailer to send an email to user.
        //here we need some parameters, like who we are going to send
        //the email ($to), the subject and the content of the email ($body)
        public function sendEmail($to, $subject, $body){
            try {
                $this->mail->addAddress($to);

                $this->mail->isHTML(true);
                $this->mail->Subject = $subject;
                $this->mail->Body = $body;
                $this->mail->AltBody = $body;
                if($this->mail->send()){
                    session_start();
                    $_SESSION['confirmStatus'] = 'recover';
                    $this->render('confirmEmail', 'layout'); 
                } else {
                    header('Location: /?request=invalidEmail');
                }
            } catch (\Exception $e) {
                echo 'error: ' . $this->mail->ErrorInfo;
            }
        }

        //in index page, in submit of the recover password form this function is going to be actived.
        //function responsable for set the variables and call to function to send the recover email,
        //checks if the user exists and create an request in database, after this will be called the
        //function responsable to send the recover email passing the parameters
        public function recoverPass(){
            $auth = new AppController();
            $auth-> validateAuth();
            $email = Container::getModel('email');
            $email->__set('email', $_POST['email']);
            $email->__set('requestType', 'recoverPass');
            $email->__set('hash', md5(rand()));

            if ($email->userExists() != null) {
                $email->recoverPass();
                $link = "localhost:8080/recover?hash={$email->__get('hash')}";
                $this->sendEmail(
                    $email->__get('email'),
                    'Recupere a senha',
                    '<a style="color:#404040;font-size:16px;line-height:1.3;text-decoration:none" href="http://'. $link.'">
                    <span style="font-size:18px;color:#1861bf">Recupere agora a senha</span>
                    <br>
                    </a>'
                );
            } 
        }

        //called in the path /recover, path /recover called by the link sent in the recover-email
        //if the request hash is valid, redirecting to the page responsable to define the new password
        public function recover(){
            $email = Container::getModel('email');
            session_start();
            $_SESSION['hash'] = $_GET['hash'];
            $_SESSION['passStatus'] = 'unAltered';
            $email->__set('hash', $_SESSION['hash']);
            if ($email->getHash() != null) {     
                $this->render('recoverPass', 'layoutEmail');
            } else {
                header('Location: /invalidRequest');
            }
        }

        //function to ask to the user the new pass and insert in the database
        public function newPass(){
            session_start();
            //if to verify if the request is valid
            if (!isset($_SESSION['passStatus'])) {
                header('Location: /invalidRequest');
                unset($_SESSION);
                session_destroy();
            }
            $email = Container::getModel('email');
            $this->__set('hash', $_SESSION['hash']);

            //if the user send an incorrect old password, he'll be redirected to the same page
            //with GET['authCode'] = 1 to show in page that the password is incorrect. but at
            //redirect the POST variable will be unset, to prevent this, we define the POST again.

            if (!isset($_POST['pass']) || !isset($_POST['oldPass'])) {
                $_POST['pass'] = $this->__get('pass');
                $_POST['oldPass'] = $this->__get('oldPass');
            } 
            //if it's the first access in the page, what means the user didn't try to access
            //an invalid request and he didn't insert the wrong old password, we'll define
            //the variables pass and oldPass by the POST
            else{
                $this->__set('pass', md5($_POST['pass']));
                $this->__set('oldPass', md5($_POST['oldPass']));
            }
            //setting the variables to User model
            $email->__set('pass', $this->__get('pass'));
            $email->__set('oldPass', $this->__get('oldPass'));
            $email->__set('hash', $this->__get('hash'));
            $email->__set('requestType', 'recoverPass');

            //if the old password sent by POST is correct, it's will be 
            //defined the new password passed in the POST
            if ($email->oldPass()['user'] == 1) {
                $email->newPass();
                $_SESSION['passStatus'] = 'altered';
                $this->confirmPage('recoverSuccess');
                //if the password is incorrect, redirecting with GET authCode = 1
            } else {
                header('Location: /recover?hash='.$this->__get('hash').'&authCode=1');
            }
        }
        //page to show the confirmation of the password change
        public function confirmPage($confirmStatus){
            $_SESSION['confirmStatus'] = $confirmStatus;
            $this->render('confirmEmail', 'layout');
        }
    }