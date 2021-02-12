<?php
    namespace App\Controllers;
    use MF\Controller\Action;
    use MF\Model\Container;
    use MF\Controller\Emails;

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

        public function recoverPass(){
            $user = Container::getModel('user');
            $user->__set('email', $_POST['email']);
            $user->__set('requestType', 'recoverPass');
            $user->__set('hash', md5(rand()));

            if ($user->userExists() != null) {
                $user->recoverPass();
                $link = "localhost:8080/recover?hash={$user->__get('hash')}";
                $this->sendEmail(
                    $user->__get('email'),
                    'Recupere a senha',
                    '<a style="color:#404040;font-size:16px;line-height:1.3;text-decoration:none" href="http://'. $link.'">
                    <span style="font-size:18px;color:#1861bf">Recupere agora a senha</span>
                    <br>
                    </a>'
                );
            } 
        }
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
        public function recover(){
            $user = Container::getModel('user');
            session_start();
            $_SESSION['hash'] = $_GET['hash'];
            $_SESSION['passStatus'] = 'unAltered';
            $user->__set('hash', $_SESSION['hash']);
            if ($user->getHash() != null) {     
                $this->render('recoverPass', 'layoutEmail');
            } else {
                header('Location: /invalidRequest');
            }
        }
        public function newPass(){
            session_start();
            if (!isset($_SESSION['passStatus'])) {
                header('Location: /invalidRequest');
                unset($_SESSION);
                session_destroy();
            }
            $user = Container::getModel('user');
            $this->__set('hash', $_SESSION['hash']);

            if (!isset($_POST['pass']) || !isset($_POST['oldPass'])) {
                $_POST['pass'] = $this->__get('pass');
                $_POST['oldPass'] = $this->__get('oldPass');
            } 
            else{
                $this->__set('pass', md5($_POST['pass']));
                $this->__set('oldPass', md5($_POST['oldPass']));
            }
            $user->__set('pass', $this->__get('pass'));
            $user->__set('oldPass', $this->__get('oldPass'));
            $user->__set('hash', $this->__get('hash'));
            $user->__set('requestType', 'recoverPass');

            if ($user->oldPass()['user'] == 1) {
                $user->newPass();
                $_SESSION['passStatus'] = 'altered';
                $_SESSION['confirmStatus'] = 'recoverSuccess';
                $this->render('confirmEmail', 'layout');
            } else {
                header('Location: /recover?hash='.$this->__get('hash').'&authCode=1');
            }
        }
        public function registerConfirm(){
            session_start();
            $_SESSION['confirmStatus'] = 'registerConfirm';
            $this->render('confirmEmail', 'layout');
        }
    }