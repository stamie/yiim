<?php

namespace app\classes;

class Nausys {

/*    private $username = "rest189@TTTTT";
    private $password = "unoXmsrk"; */
    private $username = "rest@DENTA";
    private $password = "boatrest369";

    public function getJsonCredentials() {

        return json_encode(array("username" => $this->username, "password" => $this->password));
    }

    public function getCredentials() {

        return array("username" => $this->username, "password" => $this->password);
    }
}