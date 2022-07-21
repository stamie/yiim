<?php

namespace app\classes\port;

use app\models\Port;
use app\models\Region;
use app\classes\Sync;

class PortSync extends Sync{
    private static $model = 'app\models\Port';
    protected $region_id;
    protected  $xml_long;
    protected  $xml_lat;
    protected  $wp_long;
    protected  $wp_lat;
    /**
     * 
     * Base functions 
     */

    public function __construct($ID = null, $xmlId, $xmlJsonId, $name_, $isActive = 1, 
                                $region_id,
                                $xml_long = null,
                                $xml_lat = null,
                                $wp_long = null,
                                $wp_lat = null
    ) {
        $this->id = $ID;
        $this->xml_id = $xmlId;
        $this->xml_json_id = $xmlJsonId;
        $this->name = $name_;
        $this->is_active = intval($isActive);
        $this->region_id = intval($region_id);
        $this->xml_long = floatval($xml_long);
        $this->xml_lat = floatval($xml_lat);
        $this->wp_long = floatval($wp_long);
        $this->wp_lat = floatval($wp_lat);
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
                $object->name = $this->name;
                $object->is_active = 1;
                $object->region_id = $this->region_id;
                $object->xml_long = $this->xml_long;
                $object->xml_lat = $this->xml_lat;
                return $object->save();
            }
        }
        return false;
    }
}