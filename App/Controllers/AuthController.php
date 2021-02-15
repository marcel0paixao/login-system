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
        
        //function called in path /auth, used to verify if the login variables exists
        //and are the same of any index of database, so we can redirect to home page
        public function authenticate(){
            //getting the post variables and sending to user model
            $user = Container::getModel('User');
            $user->__set('email', $_POST['email']);
            $user->__set('pass', md5($_POST['pass']));

            $email = Container::getModel('Email');
            $email->__set('email', $_POST['email']);

            //defining variable to avoid any problems with sessions variables in the page
            isset($_POST['remember']) ? $_POST['remember'] = $_POST['remember'] : $_POST['remember'] = '';
            
            //if the checkbox is checked, is defined an lastLogged variable in session, with the variables of login,
            //so if the user login with checkbox checked and logoff, the value of inputs will be defined by SESSION
            if($_POST['remember'] == 'on'){
                session_start();
                $_SESSION['lastLogged']['email'] = $user->__get('email');
                $_SESSION['lastLogged']['pass'] = $_POST['pass'];
                $_SESSION['lastLogged']['remember'] = $_POST['remember'];
            } else{
                session_start();
                unset( $_SESSION['lastLogged'] );
            }

            //calling function in User Model to verify if the user exists, if exists
            //will be returned variables of id and name
            $user->authenticate();

            //if this variables returned by authenticate function (id and name),
            //proceed to authenticate and redirect to homepage
            if($user->__get('id') != '' && $user->__get('name') != ''){
                //if the user's account isn't confirmed, we don't redirect to homepage
                //but we redirect to email confirmation page, requisiting to confirm the email
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
            //else if the user don't exists, redirecting to index page with an error code
            else {
                header('Location: /?authCode=1');
            }
        }
        //function to logoff
        public function logoff(){
            session_start();
            unset($_SESSION['id']);
            unset($_SESSION['name']);
            unset($_SESSION['email']);
            header('Location: /');
        }
    }
?>