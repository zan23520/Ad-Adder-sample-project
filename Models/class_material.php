<?php

class Material {

    public $id,
           $ime,
           $tip,
           $dimenzija,
           $velikost,
           $referenca;

    function __construct($id, $ime, $tip, $dimenzija, $velikost, $referenca){
        $this->       id = $id;
        $this->ime       = $ime;
        $this->tip       = $tip;
        $this->dimenzija = $dimenzija;
        $this->velikost  = $velikost;
        $this->referenca = $referenca;
    }
}