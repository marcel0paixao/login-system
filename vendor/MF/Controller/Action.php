<?php
    namespace MF\Controller;

    //class Action, here we render the layout and the page content
    abstract class Action{
        protected $view;
        public function __construct(){
            $this->view = new \stdClass();
        }
        //rendering the layout, requiring the requested layout
        protected function render($view, $layout){
            $this->view->page = $view;
            if (file("../../Login System/App/Views/".$layout.".phtml")) {
                require_once "../../Login System/App/Views/".$layout.".phtml";
            } else {
                $this->content();
            }
        }
        //rendering the content, requiring the requested page
        protected function content(){
            $class = get_class($this);
            $class = str_replace('App\\Controllers\\', '', $class);
            $class = strtolower(str_replace('Controller', '', $class));
            require_once "../App/Views/".$class."/".$this->view->page.".phtml";
        }
    }
?>