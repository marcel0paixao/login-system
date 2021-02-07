<?php
    namespace MF\Model;
    use App\Connection;

    //class to get the Model in App/Models and initialize the database connection, will be returned a class and the connection of Db
    class Container{
        public static function getModel($model) {
            $class = "\\App\\Models\\".ucfirst($model);
            $conn = Connection::getDb();
            return new $class($conn);
        }
    }
?>