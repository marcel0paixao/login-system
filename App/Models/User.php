<?php
    namespace App\Models;
    use MF\Model\Model;

    class User extends Model{
        private $id;
        private $name;
        private $email;
        private $pass;
        private $oldPass;
        private $birthdate;
        private $edit;
        private $hash;
        private $requestType;

        //getters and setters
        public function __get($attr){
            return $this->$attr;
        }
        public function __set($attr, $value){
            $this->$attr = $value;
        }

        //function signUp to sign up, insert the informations in the database
        public function signUp(){
            $query = "
                INSERT INTO 
                    users(name, email, pass, birthdate) 
                VALUES 
                    (:name, :email, :pass, :birthdate)
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':name', $this->__get('name'));
            $stmt->bindValue(':email', $this->__get('email'));
            $stmt->bindValue(':pass', $this->__get('pass'));
            $stmt->bindValue(':birthdate', $this->__get('birthdate'));
            $stmt->execute();

            return $this;
        }
        //validate register, verify if the information have a valid number of characters
        public function validateRegister(){
            $validate = true;
            if(strlen($this->__get('name'))<3){
                $validate = false;
            }
            if(strlen($this->__get('email'))<3){
                $validate = false;
            }
            if(strlen($this->__get('pass'))<3){
                $validate = false;
            }
            return $validate;
        }

        //getting user per id
        public function getUser(){
            $query = "
                SELECT
                    name, email, DATE_FORMAT(birthdate, '%d/%m/%Y %H:%i') AS date
                FROM
                    users
                WHERE
                    id = :id
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $this->__get('id'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        //get user per email to verify if this email passed already exists in database
        public function userExists(){
            $query = "
                SELECT
                    email
                FROM
                    users
                WHERE
                    email = :email
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':email', $this->__get('email'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        //authenticate the user, search in database the user info
        public function authenticate(){
            $query = "
                SELECT 
                    id, name, email
                FROM 
                    users
                WHERE
                    email = :email AND pass = :pass
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':email', $this->__get('email'));
            $stmt->bindValue(':pass', $this->__get('pass'));
            $stmt->execute();

            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if($user){
                if($user['id'] != '' && $user['name'] != ''){
                    $this->__set('id', $user['id']);
                    $this->__set('name', $user['name']);
                } 
            }
            return $this;
        }
        /*
        * Function edit, this function update the database with
        * the requested variable as the requested value.
        * Here I have and problem, for some reason the bindValue
        * was no working corretly, so I used dots.
        */
        public function edit(){
            $query = "
                UPDATE users SET ".$this->__get('edit')." = '".$this->__get($this->__get('edit'))."' WHERE id = :id
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $this->__get('id'));
            $stmt->execute();
            print_r($this->getUser());
            print_r($query);

            return $this->getUser();
        }
        //function do delete the related account using id
        public function delete(){
            $query = "
                DELETE FROM 
                    `users` 
                WHERE 
                    id = :id
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $this->__get('id'));
            $stmt->execute();
        }
        //function to count users with the id passed
        public function confirmDelete(){
            $query = "
                SELECT 
                    count(*) as user
                FROM 
                    users
                WHERE
                    id = :id AND pass = :pass
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $this->__get('id'));
            $stmt->bindValue(':pass', $this->__get('pass'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
    }
?>