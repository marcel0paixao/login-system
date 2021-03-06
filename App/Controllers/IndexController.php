<?php
    namespace App\Controllers;
    use MF\Controller\Action;
    use MF\Model\Container;
    use App\Controllers\EmailController;

    /*
    * class indexController will define the non functional requirements of the 
    * index actions. 
    * EVERY controller class have to extend the Action class to render solicited webpages
    */
    class indexController extends Action{
        public function __construct(){
            $this->view = new \stdClass;
        }
        //function index, here it's decided if the client has and valid session, will be redirected to home webpage
        public function index(){
            $this->view->login = isset($_GET['login']) ? $_GET['login'] : '';
            session_start();
            //verifying if id, name, and lastLogged variables in session are set, 
            //if not will be set to solve any possible print errors in the index page
            if (isset($_SESSION['id']) && isset($_SESSION['name']) && isset($_SESSION['lastLogged'])) {
                //if id and name are not null, will be redirected to home page
                if ($_SESSION['id'] != '' && $_SESSION['name'] !== ''){
                    header('Location: /home');
                }
            } else {
                    //defining the necessary session variables
                    (isset($_SESSION['lastLogged'])) ? $_SESSION['lastLogged'] = $_SESSION['lastLogged'] : $_SESSION['lastLogged'] = array(
                        'email' => null,
                        'pass' => null
                    );
                    $_GET['authCode'] = isset($_GET['authCode']) ? $_GET['authCode'] : '';
                    $_SESSION['remember'] = isset($_SESSION['remember']) ? $_SESSION['remember'] : '';
            }
            $this->render('index', 'layout');
        }
        //function to render the homepage
        public function home(){
            $this->render('home', 'layoutHome');
        }
        //function to render the 404 not found page
        public function notFound(){
            $this->render('404', 'layout');
        }
        //function to register the client
        public function register(){
            //calling the User model to set variables send from POST
            $user = Container::getModel('User');

            $user->__set('name', $_POST['name']);
            //I prefer to use md5 cryptography, but use what you prefeer
            $user->__set('pass', md5($_POST['pass']));
            $user->__set('email', $_POST['email']);
            $user->__set('birthdate', $_POST['birthdate']);

            //calling functions validateRegister and userExists to verify if we can register this user, if sucessfully, rendering the email confirmation page
            if ($user->validateRegister() && $user->userExists() == null){
                $user->signUp();
                $email = new EmailController();
                $email->__set('email', $_POST['email']);
                $email->confirmEmail();
                $email->confirmPage('confirmEmail');
            } else {
                //if has an error to registrate the user, will be redirected to index page and defined registerError variables so 
                //we can treat this in index page printing any message errors, I didn't yet treat this yet
                $this->view->user = array(
                    'name' => $_POST['name'],
                    'email' => $_POST['email']
                );
                $this->view->registerError = true;
                header('Location: /');
            }
        }
        //if the user try to access a non-valid page, will be redirected
        public function invalidRequest(){
            $this->render('invalidRequest', 'layout');
        }
    }
?>