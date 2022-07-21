<?php

namespace app\classes;

class Sync {
    protected $id;
    protected $xml_id;
    protected $xml_json_id;
    protected $name;
    protected $is_active;
    public static $dateString = 'Y-m-d H:i:s';

    public function __construct($ID = null, $xmlId, $xmlJsonId, $isActive = 1){
        $this->id = $ID;
        $this->xml_id = $xmlId;
        $this->xml_json_id = $xmlJsonId;
        $this->is_active = intval($isActive);
    }
}