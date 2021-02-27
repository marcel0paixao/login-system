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
    class AppController extends Action{
        //function to verify if the user is valid, this function must to be called every time that
        //the client require a webpage, to verify if he can or not see that webpage
        public function validateAuth(){
            session_start();
            //if the client try to access a webpage that need authtentication, redirecting to index page with and GET code error
            if(!isset($_SESSION['id']) || $_SESSION['id'] == '' && !isset($_SESSION['name']) || $_SESSION['name'] == ''){
                header('Location: /?authCode=2');
                session_start();
                session_destroy();
                unset( $_SESSION );
            }
        }
        //page to, if the user is valid (calling the validateAuth function), 
        //set the id session, the global variable view and render the homepage
        public function home(){
            $this->validateAuth();
            
            $user = Container::getModel('user');
            $user->__set('id', $_SESSION['id']);
            $this->view->info = $user->getUser();

            $this->render('home', 'layoutHome');
        }
        //this function will be called every time that the client 
        //request to edit some information about his account
        public function edit(){
            $this->validateAuth();

            $user = Container::getModel('user');
            //setting the id that the info will be edited
            $user->__set('id', $_SESSION['id']);
            //setting what information the user wan't to edit
            $user->__set('edit', $_POST['info']);
            //setting what's the value of the information, 
            //we can se this part as $user->__set(edit, value)
            $user->__set($_POST['info'], $_POST[$_POST['info']]);
            //setting the global variable info as the return of the edit() function
            $this->view->info = $user->edit();

            header('Location: /home');
        }
        /*
        * Here we have 2 functions with mostly the same name.
        * deleteAccount is the function to in fact delete the user,
        * in the delete page we require the password as a pass of security
        * to delete the account, this function verify if the 
        * password passed by POST is valid, if not we redirect with a
        * GET code error.
        * 
        * The function called deleteConfirmation have to be called 
        * after the account be deleted, and just render the 
        * page that show to the user that the account was sucessfully deleted
        */
        
        public function deleteAccount(){
            $this->validateAuth();
            $user = Container::getModel('user');
            $user->__set('id', $_SESSION['id']);
            $user->__set('pass', md5($_POST['pass']));
            //verify if the user really exists, to delete.
            if ($user->confirmDelete()['user'] == 1) {
                $user->delete();
                $email = Container::getModel('email');
                $email->__set('email', $_SESSION['email']);
                $email->__set('requestType', 'confirmAccount');
                $email->__set('hash', $email->requestAlreadyExists()['hash']);
                $email->setStatus();
                $this->render('confirmDelete', 'layoutHome');
                session_destroy();
                unset( $_SESSION );
            } else{
                header('Location: /deleteConfirmation?authCode=1');
            }
        }
        public function deleteConfirmation(){
            $this->validateAuth();
            $this->render('delete', 'layoutHome');
        }
    }
?>