<?php

namespace netvod\classes;


interface Programme {

    public function getTitre() : string;
    public function getSynopsis() : string;
    public function getImage() : string;
}