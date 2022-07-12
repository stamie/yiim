<?php

namespace app\classes\pricemeasure;

use app\models\PriceMeasure;
use app\models\Region;

class PriceMeasureSync {
    private static $model = 'app\models\PriceMeasure';
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
     * PriceMeasure functions 
     */
    public function __construct($ID = null, $wpId = 0, $wpPrefix, $xmlId, $xmlJsonId, $isActive = 1, $name) {
        $this->id = $ID;
        $this->xml_id = $xmlId;
        $this->xml_json_id = $xmlJsonId;
        $this->name = $name;
        $this->is_active = intval($isActive);
    }
    /**
     * 
     * Additional functions 
     */

    public function getRegionId (){
        return $this->country_id;
    }
    public function getRegion (){
        $region = Region::findOne(['xml_json_id' => $this->region_id, 'xml_id' => $this->xml_id]);
        return $region;
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
                $object->name = $this->name;
                if ( $object->save() ) {
                    return $object;
                }
                var_dump($object); exit;
            }
        }
        return false;
    }

}

?>