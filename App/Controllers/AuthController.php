<?php
    namespace App\Controllers;
    use MF\Controller\Action;
    use MF\Model\Container;
    use App\Controllers\EmailController;

    /*
    * class authController will define the non functional requirements of the 
    * authentication, deciding if the user is valid or not. 
    * EVERY controller class have to extend the Action class to render solicited webpages
    */
    class AuthController extends Action{
        
        public function authenticate(){
            $user = Container::getModel('User');
            $user->__set('email', $_POST['email']);
            $user->__set('pass', md5($_POST['pass']));

            $email = Container::getModel('Email');
            $email->__set('email', $_POST['email']);

            isset($_POST['remember']) ? $_POST['remember'] = $_POST['remember'] : $_POST['remember'] = '';
            
            if($_POST['remember'] == 'on'){
                session_start();
                $_SESSION['lastLogged']['email'] = $user->__get('email');
                $_SESSION['lastLogged']['pass'] = $_POST['pass'];
                $_SESSION['lastLogged']['remember'] = $_POST['remember'];
            } else{
                session_start();
                unset( $_SESSION['lastLogged'] );
            }

            $user->authenticate();

            if($user->__get('id') != '' && $user->__get('name') != ''){
                if ($email->getEmailConfirmation()['status'] == 0) {
                    $email2 = new EmailController();
                    $email2->confirmPage('tryied to login without confirmation');
                    $_SESSION['resendEmail'] = $_POST['email'];
                }
                else{
                    session_start();
                    $_SESSION['id'] = $user->__get('id');
                    $_SESSION['name'] = $user->__get('name');
                    $_SESSION['remember'] = $_POST['remember'];
                    $_SESSION['email'] = $_POST['email'];
                    header('Location: /home');
                }
            }
            else {
                header('Location: /?authCode=1');
            }
        }
        public function logoff(){
            session_start();
            unset($_SESSION['id']);
            unset($_SESSION['name']);
            header('Location: /');
        }
    }
?>