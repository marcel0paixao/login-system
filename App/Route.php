<?php
    namespace App;
    use MF\Init\Bootstrap;

    class Route extends Bootstrap{
        /*
        * Class Route, where we define the routes, extends Bootstrap to use his functions.
        * Function initRoutes, here we define the routes and if requested, we send who's 
        * the responsible controller and his action (function that will be called in the controller).
        */
        public function initRoutes(){
            $routes['/'] = array(
                'route' => '/',
                'controller' => 'indexController',
                'action' => 'index'
            );
            $routes['notFound'] = array(
                'route' => '/404',
                'controller' => 'indexController',
                'action' => 'notFound'
            );
            $routes['auth'] = array(
                'route' => '/auth',
                'controller' => 'AuthController',
                'action' => 'authenticate'
            );
            $routes['logoff'] = array(
                'route' => '/logoff',
                'controller' => 'AuthController',
                'action' => 'logoff'
            );
            $routes['home'] = array(
                'route' => '/home',
                'controller' => 'appController',
                'action' => 'home'
            );
            $routes['register'] = array(
                'route' => '/register',
                'controller' => 'indexController',
                'action' => 'register'
            );
            $routes['edit'] = array(
                'route' => '/edit',
                'controller' => 'AppController',
                'action' => 'edit'
            );
            $routes['deleteAccount'] = array(
                'route' => '/deleteAccount',
                'controller' => 'AppController',
                'action' => 'deleteAccount'
            );
            $routes['deleteConfirmation'] = array(
                'route' => '/deleteConfirmation',
                'controller' => 'AppController',
                'action' => 'deleteConfirmation'
            );
            $routes['recoverPass'] = array(
                'route' => '/recoverPass',
                'controller' => 'EmailController',
                'action' => 'recoverPass'
            );
            $routes['recover'] = array(
                'route' => '/recover',
                'controller' => 'EmailController',
                'action' => 'recover'
            );
            $routes['newPass'] = array(
                'route' => '/newPass',
                'controller' => 'EmailController',
                'action' => 'newPass'
            );
            $routes['invalidRequest'] = array(
                'route' => '/invalidRequest',
                'controller' => 'indexController',
                'action' => 'invalidRequest'
            );
            $routes['confirmAccount'] = array(
                'route' => '/confirmAccount',
                'controller' => 'emailController',
                'action' => 'confirmAccount'
            );
            $routes['resendRegisterConfirm'] = array(
                'route' => '/resendRegisterConfirm',
                'controller' => 'emailController',
                'action' => 'resendRegisterConfirm'
            );
            $routes['resendRecoverPass'] = array(
                'route' => '/resendRecoverPass',
                'controller' => 'emailController',
                'action' => 'resendRecoverPass'
            );
            //calling the setRoutes defined in Bootstrap class passing an array of exist routes
            $this->setRoutes($routes);
        }
    }
?>