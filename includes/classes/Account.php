<?php
class Account
{

    private $con;
    private $errorArray = array();

    public function __construct($con)
    {
        $this->con = $con;
    }
    public function register($fn, $ln, $un, $em, $em2, $pw, $pw2)
    {

        $this->validateFirstName($fn);
        $this->validateLastName($ln);
        $this->validateUsername($un);
        $this->ValidateEmails($em, $em2);
        $this->validatePasswords($pw, $pw2);
        

        if (empty($this->errorArray)) {
            return $this->insertUserDetails($fn, $ln, $un, $em, $pw);
        }

        return false;
    }

    public function login($un, $pw)
    {

        $pw = hash("sha512", $pw);

        $query = $this->con->prepare("SELECT * FROM users WHERE username=:un AND password=:pw");

        // Bind the values to the placeholders
        $query->bindValue(":un", $un);

        $query->bindValue(":pw", $pw);

        // Execute the query and return the result
         $query->execute();

         if($query->rowCount() == 1){
            return true;
         }

         array_push($this->errorArray,Constants::$loginFailed);
         return false;
    }

    private function insertUserDetails($fn, $ln, $un, $em, $pw)
    {
        // Hash the password using SHA-512
        $pw = hash("sha512", $pw);

        // Corrected SQL query with the closing parenthesis for the column names
        $query = $this->con->prepare("INSERT INTO users (firstName, lastName, username, email, password)
                                      VALUES (:fn, :ln, :un, :em, :pw)");

        // Bind the values to the placeholders
        $query->bindValue(":fn", $fn);
        $query->bindValue(":ln", $ln);
        $query->bindValue(":un", $un);
        $query->bindValue(":em", $em);
        $query->bindValue(":pw", $pw);

        // Execute the query and return the result
        return $query->execute();
    }


    private function validateFirstName($fn)
    {

        if (strlen($fn) < 2 || strlen($fn) > 25) {
            array_push($this->errorArray, Constants::$firstNameCharacters);
        }
    }

    private function validateLastName($ln)
    {

        if (strlen($ln) < 2 || strlen($ln) > 25) {
            array_push($this->errorArray, Constants::$lastNameCharacters);
        }
    }

    private function validateUsername($un)
    {
        // Check the length of the username
        if (strlen($un) < 2 || strlen($un) > 25) {
            array_push($this->errorArray, Constants::$usernameCharacters);
            return;
        }

        // Prepare the SQL query with the correct placeholder
        $query = $this->con->prepare("SELECT * FROM users WHERE username = :un");

        // Bind the value to the placeholder
        $query->bindValue(":un", $un);

        // Execute the query
        $query->execute();

        // Check if the username already exists in the database
        if ($query->rowCount() != 0) {
            array_push($this->errorArray, Constants::$usernameTaken);
        }
    }

    private function ValidateEmails($em, $em2)
    {
        if ($em != $em2) {
            array_push($this->errorArray, Constants::$emailsDontMatch);
            return;
        }
        if (!filter_var($em, FILTER_VALIDATE_EMAIL)) {
            array_push($this->errorArray, Constants::$emailInvalid);
            return;
        }
        // Prepare the SQL query with the correct placeholder
        $query = $this->con->prepare("SELECT * FROM users WHERE email = :em");

        // Bind the value to the placeholder
        $query->bindValue(":em", $em);

        // Execute the query
        $query->execute();

        // Check if the username already exists in the database
        if ($query->rowCount() != 0) {
            array_push($this->errorArray, Constants::$emailTaken);
        }
    }

    private function validatePasswords($pw, $pw2)
    {
        if ($pw != $pw2) {
            array_push($this->errorArray, Constants::$passwordsDontMatch);
            return;
        }
        if (strlen($pw) < 5 || strlen($pw) > 25) {
            array_push($this->errorArray, Constants::$passwordLength);
        }
    }

    public function getError($error)
    {
        if (in_array($error, $this->errorArray)) {
            return "<span class ='errorMessage'>$error</span>";
        }
    }
}
