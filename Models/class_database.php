<?php

class DB
{
    //private $user = 'root';
    //private $pass = '';
    private $conn = NULL;
    

    public function __construct()
    {
        //return $this->connect();
        $user = 'root';
        $pass = '';
        try
        {
            $this->conn = new PDO('mysql:host=localhost;dbname=ad-adder', $user, $pass);
            $this->conn -> setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } 
        catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
        $this->conn;
    }

    public function init(){
        return $this->conn;
    }

}

//EOF