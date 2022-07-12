<?php

namespace app\classes\region;

use app\models\Region;
use app\models\Country;

class RegionSync {
    private static $model = 'app\models\Region';
    
    protected $id;
    protected $wp_id;
    protected $wp_prefix;
    protected $xml_id;
    protected $xml_json_id;
    protected $name;
    protected $is_active;

    protected $country_id;

    /**
     * 
     * Base functions 
     */

    public function __construct($ID = null, $xmlId, $xmlJsonId, $name_, $isActive = 1, $country_id)
    {
        $this->id = $ID;
        $this->xml_id = $xmlId;
        $this->xml_json_id = $xmlJsonId;
        $this->name = $name_;
        $this->is_active = intval($isActive);
        $this->country_id = intval($country_id);
    }
    /**
     * 
     * Additional functions 
     */
    public function getCountryId (){
        return $this->country_id;
    }
    public function getCountry (){
        $country = Country::findOne(['xml_json_id' => $this->country_id, 'xml_id' => $this->xml_id]);
        return $country;
    }
    /**
     * 
     * Syncrons function
     */
    public function sync () {
        if ($this) {
            $condition = [
                'xml_id' => $this->xml_id,
                'xml_json_id' => $this->xml_json_id,
            ];
            $object = self::$model::findOne($condition);
            if ($object){
                $object->is_active = 1;
                return $object->save(0);
            } else {
                $object = new self::$model();
                $object->xml_id = $this->xml_id;
                $object->xml_json_id = $this->xml_json_id;
                $object->name = $this->name;
                $object->is_active = 1;
                $object->country_id = $this->country_id;
                return $object->save();
            }
        }
        return false;
    }
}