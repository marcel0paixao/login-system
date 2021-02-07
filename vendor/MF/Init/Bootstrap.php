<?php
    namespace MF\Init;

    //class Bootstrap to create class instance's and to call his function
    abstract class Bootstrap{
        private $routes;

        //abstract function initRoutes, that must to be implanted for the class which have herancy
        abstract protected function initRoutes();

        //constructor, getters and setters
        public function __construct(){
            $this->initRoutes();
            $this->run($this->getUrl());
        }
        public function getRoutes(){
            return $this->routes;
        }
        //function setRoutes called in class Route, here we recieve $routes as an array
        public function setRoutes(array $route){
            $this->routes = $route;
        }
        //the 'run' function search for any route that's the same as requested in the call of function
        protected function run($url){
            //$get404, the route don't exists until be proved by the If bellow, using this method 
            //'cause if we redirect in the foreach, we will always be redirected to /404 path
            $get404 = true;
            foreach ($this->getRoutes() as $key => $route) {
                //calling the function get404 to verify if the requested route exists, if exists, redirecting to the requested route
                if(!$this->get404($route['route'])){
                    $get404 = false;
                    $class = "App\\Controllers\\" . ucfirst($route['controller']);
                    $controller = new $class;
                    $action = $route['action'];
                    $controller->$action();
                }
            }
            //if not found the requested path, client will be redirected to /404 path
            if($get404) {
                header('Location: /404');
            }
        }
        //function to verify if actual URI is the same as the requested path passed in the call of the function
        public function get404($route){
            if ($this->getUrl() != $route) {
                return true;
            }
        }
        //function to get the actual URL path
        protected function getUrl(){
            return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        }
    }
?>