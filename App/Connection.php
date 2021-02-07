<?php
    namespace App;

    class Connection{
        //function to initialize connection to the database, here we need the info of who is the host ($host), what's the database ($db) and login credentials of the database as $user and $pass
        public static function getDb(){
            $host = "localhost";
            $db = "login";
            $user = "root";
            $pass = "";
            //trying the connection, if failed, will be print a message error with the details.
            try{
                $conn = new \PDO(
                    "mysql:host=$host;dbname=$db;charset=utf8",
                    $user,
                    $pass
                );
                return $conn;
            }catch(\PDOException $e){
                echo $e->getMessage();
            }
        }
    }
?>