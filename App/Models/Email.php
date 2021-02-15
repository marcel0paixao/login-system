<?php
    namespace App\Models;
    use MF\Model\Model;

    class Email extends Model{
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

        //registering that the user requested a password change
        public function recoverPass(){
            $query = "
                INSERT INTO
                    email_request (email, hash, type)
                VALUES (:email, :hash, :type)
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':email', $this->__get('email'));
            $stmt->bindValue(':hash', $this->__get('hash'));
            $stmt->bindValue(':type', $this->__get('requestType'));
            $stmt->execute();
        }

        //verifying if the requested hash exists and if the hash is valid by status
        public function getHash(){
            $query = "
                SELECT 
                    *
                FROM 
                    email_request
                WHERE
                    hash = :hash AND status = 0
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':hash', $this->__get('hash'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        //getting status
        public function getStatus(){
            $query = "
                SELECT 
                    status
                FROM 
                    email_request
                WHERE
                    hash = :hash
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':hash', $this->__get('hash'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        //getting status
        public function setStatus(){
            $query = "
                UPDATE
                    email_request
                SET
                    STATUS = !STATUS
                WHERE
                    hash = :hash
                ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':hash', $this->__get('hash'));
            $stmt->execute();
        }

        //defining the new pass for the requested user
        public function newPass(){
            $query = "
                UPDATE
                    users
                SET
                    pass = :pass
                WHERE
                    email = (select email from email_request where hash = :hash AND type = :type)
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':pass', $this->__get('pass'));
            $stmt->bindValue(':hash', $this->__get('hash'));
            $stmt->bindValue(':type', $this->__get('requestType'));
            $stmt->execute();
            //setting status to 1 to don't allow the system to use the same register
            $this->setStatus();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        
        //verifying if the old pass passed by POST is correct
        public function oldPass(){
            $query = "
                SELECT
                    count(*) as user
                FROM
                    users
                WHERE
                    email = (
                        SELECT 
                            email 
                        FROM 
                            email_request 
                        WHERE 
                            hash = :hash)
                    AND pass = :oldPass
                ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':hash', $this->__get('hash'));
            $stmt->bindValue(':oldPass', $this->__get('oldPass'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        //inserting the request in database
        public function confirmEmail(){
            $query = "
                INSERT INTO
                    email_request (email, hash, type, status)
                VALUES (:email, :hash, :type, :status)
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':email', $this->__get('email'));
            $stmt->bindValue(':hash', $this->__get('hash'));
            $stmt->bindValue(':type', $this->__get('requestType'));
            $stmt->bindValue(':status', '0');
            $stmt->execute();
        }
        //function to verify if the account is confirmed
        public function getEmailConfirmation(){
            $query = "
                SELECT
                    status
                FROM
                    email_request
                WHERE
                    email = (
                        SELECT
                            email
                        FROM
                            users
                        WHERE
                            email = :email
                    ) AND type = 'confirmAccount'
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':email', $this->__get('email'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        //to verify if the the request already exists
        public function requestAlreadyExists(){
            $query = "
                SELECT
                    *
                FROM
                    email_request
                WHERE
                    email = :email AND type = :type
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':email', $this->__get('email'));
            $stmt->bindValue(':type', $this->__get('requestType'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        //to get the hash by email
        public function getHashPerEmail(){
            $query = "
                SELECT
                    hash
                FROM
                    email_request
                WHERE
                    email = :email AND type = :type
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':email', $this->__get('email'));
            $stmt->bindValue(':type', $this->__get('requestType'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
    }
?>