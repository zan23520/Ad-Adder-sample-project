<?php

class User {

    public $name,
           $userId;

    function __construct($id, $name) {
        $this->userId = $id;
        $this->name   = $name;
    }
}