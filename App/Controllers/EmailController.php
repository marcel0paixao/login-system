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
        public function recoverPass(){
            $user = Container::getModel('user');
            $user->__set('email', $_POST['email']);
            $this->hash = md5(rand());
            $user->__set('hash', $this->hash);

            if ($user->userExists() != null) {
                $user->recoverPass();
                $link = "localhost:8080/recover?hash={$user->__get('hash')}";
                $this->sendEmail(
                $user->__get('email'),
                    'titulo',
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
                $this->mail->send();
            } catch (\Exception $e) {
                echo 'error: ' . $this->mail->ErrorInfo;
            }
        }
        public function recover(){
            $user = Container::getModel('user');
            $user->__set('hash', $_GET['hash']);
            if ($user->getHash() != null) {
                $this->render('recoverPass', 'layoutEmail');
            } else {
                header('Location: /invalidRequest');
            }
        }
        public function newPass(){
            $user = Container::getModel('user');
            $user->__set('pass', md5($_POST['pass']));
            $user->__set('hash', $this->hash);
            echo $this->hash;
            print_r($_POST);
            echo $user->newPass();
        }
    }
    