<?php

namespace app\classes\country;

use app\models\Country;

class CountrySync {

    public $id;
    //public int $wp_id = 1;

    public $wp_prefix;
    public $xml_id;
    public $xml_json_id;

    public $code;
    public $name;
    public $is_active;

    public function __construct($ID = null, $wpId = 0, $wpPrefix, $xmlId, $xmlJsonId, $code_, $name_, $isActive = 1)
    {
        $this->id = $ID;
        
        
        $this->xml_id = intval($xmlId);
        $this->xml_json_id = intval($xmlJsonId);
        $this->code = $code_;
        $this->name = $name_;
        $this->is_active = intval($isActive);

    }

    public function getId (){
        return $this->id;
    }
    public function getWpId (){
        return $this->wp_id;
    }
    public function getWpPrefix (){
        return $this->wp_prefix;
    }
    public function getXmlId (){
        return $this->xml_id;
    }
    public function getXmlJsonId (){
        return $this->xml_json_id;
    }
    public function getCode (){
        return $this->code;
    }
    public function getName (){
        return $this->name;
    }
    public function getIsActive (){
        return $this->is_active;
    }

    public function syncCountry () {


        if ($this) {

            $condition = [
                
                'xml_id' => $this->xml_id,
                'xml_json_id' => $this->xml_json_id,

            ];

            $country = Country::findOne($condition);
            if ($country){
                $country->is_active = 1;

                $country->save();
                
                return $country->save();
            } else {
                $country = new Country();
                $country->wp_id = $this->wp_id;
                $country->wp_prefix = $this->wp_prefix;
                $country->xml_id = $this->xml_id;
                $country->xml_json_id = $this->xml_json_id;
                $country->code = $this->code;
                $country->name = $this->name;
                $country->is_active = $this->is_active;

                return $country->save();
                return 1;
            }

            return false;
        }

        return false;
    }

}

?>