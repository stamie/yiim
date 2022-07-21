<?php
namespace app\classes\base;
use app\models\Region;
use app\classes\Sync;
class BaseSync extends Sync{
    private static $model = 'app\models\Base';
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
    public function __construct($ID = null, $xmlId, $xmlJsonId, $isActive = 1, 
                                $location_id, 
                                $company_id,
                                $check_in_time,
                                $check_out_time,
                                $xml_long = null,
                                $xml_lat = null,
                                $wp_long = null,
                                $wp_lat = null
    ) {
        parent::__construct($ID, $xmlId, $xmlJsonId, $isActive);
        $this->location_id = $location_id;
        $this->company_id = $company_id;
        $this->check_in_time = $check_in_time;
        $this->check_out_time = $check_out_time;
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