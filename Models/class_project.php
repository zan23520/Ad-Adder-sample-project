<?php

class Project { 

    public $id,
           $uid,
           $ime,
           $oglasevalec,
           $termin,
           $format,
           $material;

    function __construct($id, $uid, $ime, $oglasevalec, $termin, $format, $material) {
        $this->id          = $id;
        $this->uid         = $uid;
        $this->ime         = $ime;
        $this->oglasevalec = $oglasevalec;
        $this->termin      = $termin;
        $this->format      = $format;
        $this->material    = $material;
    }
}