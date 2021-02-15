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
        private $email;
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
        public function sendEmail($to, $subject, $body, $confirmStatus){
            try {
                $this->mail->addAddress($to);

                $this->mail->isHTML(true);
                $this->mail->Subject = $subject;
                $this->mail->Body = $body;
                $this->mail->AltBody = $body;
                if($this->mail->send()){
                    $_SESSION['confirmStatus'] = $confirmStatus;
                    return true;
                } else {
                    //header('Location: /?request=invalidEmail');
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
            session_start();
            $email = Container::getModel('email');
            if (!isset($_POST['email']) && !isset($_POST['btn'])) {
                //header('Location: /');
            }
            //it's going to verify if we are accessing the path by the button, and if not
            //will be created a new request in the dabase and sent an email.
            //if non-satisfy will be get the hash in database and send an email with the link with the hash
            if (!isset($_POST['btn'])) {
                $email->__set('email', $_POST['email']);
                $email->__set('requestType', 'recoverPass');
                $email->__set('hash', md5(rand()));
                $_SESSION['resendEmail'] = $_POST['email'];
                $_SESSION['resendHash'] =  $email->__get('hash');

                if ($email->userExists() != null) {
                    $email->recoverPass();
                    $link = "localhost:8080/recover?hash={$email->__get('hash')}";
                    if($this->sendEmail(
                        $email->__get('email'),
                        'Recupere a senha',
                        '<a style="color:#404040;font-size:16px;line-height:1.3;text-decoration:none" href="http://'. $link.'">
                        <span style="font-size:18px;color:#1861bf">Recupere agora a senha</span>
                        <br>
                        </a>',
                        'recover'
                    )){
                        $this->confirmPage('recover');
                    }
                }
            } else {
                $email->__set('email', $_SESSION['resendEmail']);
                $email->__set('requestType', 'recoverPass');
                $email->__set('hash', $_SESSION['resendHash']);

                if ($email->userExists() != null) {
                    $link = "localhost:8080/recover?hash={$email->__get('hash')}";
                    if($this->sendEmail(
                        $email->__get('email'),
                        'Recupere a senha',
                        '<a style="color:#404040;font-size:16px;line-height:1.3;text-decoration:none" href="http://'. $link.'">
                        <span style="font-size:18px;color:#1861bf">Recupere agora a senha</span>
                        <br>
                        </a>',
                        'recover'
                    )){
                        $this->confirmPage('recover');
                    }
                }
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
        //confirmation of the email after register
        public function confirmEmail(){
            $email = Container::getModel('email');
            $email->__set('hash', md5(rand()));
            $email->__set('email', $this->__get('email'));
            $email->__set('requestType', 'confirmAccount');
            //setting the link that will be send by email
            $link;

            //we verify if this is the request already exists in database to don't duply the requests
            if ($email->requestAlreadyExists() == null) {
                $email->confirmEmail();
                //if the request don't exists, will be sent an link with a hash generated by this instace
                $link = "localhost:8080/confirmAccount?hash={$email->__get('hash')}";
            }
            else{
                //if the request already exists, we will request the hash and send the email with the hash
                $link = "localhost:8080/confirmAccount?hash={$email->requestAlreadyExists()['hash']}";
            }
            //after the method send an email successfully, will be render a confirmation page
            if($this->sendEmail(
                $email->__get('email'),
                'Confirme sua conta',
                '<a style="color:#404040;font-size:16px;line-height:1.3;text-decoration:none" href="http://'. $link.'">
                    <span style="font-size:18px;color:#1861bf">Confirme sua conta</span>
                    <br>
                </a>',
                'confirmEmail'
            )){
                $this->confirmPage('confirmEmail');
            }

        }
        //in the path /confirmAccount called in the click at the email link, we get the GET variable and
        //set the status of the confirmation to true, now the user will be able to use his account.
        //if the hash already exists, will be redirected to an invalid request page
        public function confirmAccount(){
            $email = Container::getModel('email');
            $email->__set('hash', $_GET['hash']);
            if ($email->getHash() != null) {   
                $this->render('accountConfirmed', 'layoutEmail');
                $email->setStatus();
            } else {
                header('Location: /invalidRequest');
            }
        }
        //we call this function every time the user click on the button to resend the confirmation email
        //will be re-send an confirmation email
        public function resendRegisterConfirm(){
            session_start();
            //we get the SESSION['resendEmail'] variable defined in AuthController and set te email
            //to Email Model so we can use his functions to send the email with the hash
            if (!isset($_SESSION['resendEmail'])) {
                header('Location: /');
            }
            $email = Container::getModel('email');
            $email->__set('email', $_SESSION['resendEmail']);
            $email->__set('requestType', 'confirmAccount');
            $link;

            if ($email->requestAlreadyExists() == null) {
                $link = "localhost:8080/confirmAccount?hash={$email->__get('hash')}";
            }
            else{
                $link = "localhost:8080/confirmAccount?hash={$email->requestAlreadyExists()['hash']}";
            }
            if($this->sendEmail(
                $email->__get('email'),
                'Confirme sua conta',
                '<a style="color:#404040;font-size:16px;line-height:1.3;text-decoration:none" href="http://'. $link.'">
                    <span style="font-size:18px;color:#1861bf">Confirme sua conta</span>
                    <br>
                </a>',
                'confirmEmail'
            )){
                $this->confirmPage('resendConfirmEmail');
            }
        }
    }