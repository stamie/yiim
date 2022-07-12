<?php

namespace app\classes\base;

use app\models\Base;
use app\models\Region;

class BaseSync {
    private static $model = 'app\models\Base';
    
    protected $id;
    protected $wp_id;
    protected $wp_prefix;
    protected $xml_id;
    protected $xml_json_id;
    protected $name;
    protected $is_active;

    protected $location_id;
    protected $company_id;
    protected $check_in_time;
    protected $check_out_time;
    protected $xml_long;
    protected $xml_lat;
    protected $wp_long;
    protected $wp_lat;

    /**
     * 
     * Base functions 
     */

    public function __construct($ID = null, $wpId = 0, $wpPrefix, $xmlId, $xmlJsonId, $isActive = 1, 
                                $location_id, 
                                $company_id,
                                $check_in_time,
                                $check_out_time,
                                $xml_long = null,
                                $xml_lat = null,
                                $wp_long = null,
                                $wp_lat = null
    ) {

        $this->id = $ID;
        
        
        $this->xml_id = $xmlId;
        $this->xml_json_id = $xmlJsonId;
        //$this->name = $name_;
        $this->is_active = intval($isActive);
        
        $this->location_id = $location_id;
        $this->company_id = $company_id;
        $this->check_in_time = $check_in_time;
        $this->check_out_time = $check_out_time;
        $this->xml_long = floatval($xml_long);
        $this->xml_lat = floatval($xml_lat);
        $this->wp_long = floatval($wp_long);
        $this->wp_lat = floatval($wp_lat);

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

    public function getIsActive (){
        return $this->is_active;
    }

    /**
     * 
     * Additional functions 
     */

    public function getRegionId (){
        return $this->country_id;
    }

    public function getRegion (){
        
        $region = Region::findAll(['xml_json_id' => $this->region_id, 'xml_id' => $this->xml_id]);
        if ($region)
            return $region[0];
        return false;
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
                $object->is_active = 1;

                $object->location_id = $this->location_id;
                $object->company_id = $this->company_id;
                $object->check_in_time = $this->check_in_time;
                $object->check_out_time = $this->check_out_time;
                $object->xml_long = $this->xml_long;
                $object->xml_lat = $this->xml_lat;
                
                return $object->save();

                
                
            }

        }

        return false;
    }

}

?>