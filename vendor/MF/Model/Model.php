<?php
    namespace MF\Model;

    //abstract class to recieve the Db connection and save the Db connection
    abstract class Model{
        protected $db;

        public function __construct(\PDO $db){
            $this->db = $db;
        }
    }
?>