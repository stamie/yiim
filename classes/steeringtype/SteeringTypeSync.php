<?php
namespace app\classes\steeringtype;
use app\models\SteeringType;
use app\classes\Sync;
class SteeringTypeSync extends Sync{
    private static $model = 'app\models\SteeringType';
    /**
     * 
     * Base functions 
     */
    public function __construct($ID = null, $xmlId, $xmlJsonId, $name_, $isActive = 1) {
        $this->id = $ID;
        $this->xml_id = $xmlId;
        $this->xml_json_id = $xmlJsonId;
        $this->name = $name_;
        $this->is_active = intval($isActive);
    }
    /**
     * 
     * Additional functions 
     */

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
                return $object->save();
            } else {
                $object = new self::$model();
                $object->xml_id = $this->xml_id;
                $object->xml_json_id = $this->xml_json_id;
                $object->name = $this->name;
                $object->is_active = 1;
                return $object->save();
            }
        }
        return false;
    }
}