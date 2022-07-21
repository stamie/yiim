<?php

namespace app\classes\equipment;
use app\classes\Sync;

class EquipmentSync extends Sync{
    private static $model = 'app\models\Equipment';
    protected $name;
    protected $equipment_category_json_id;
    /**
     * 
     * Base functions 
     */

    public function __construct($ID = null, $xmlId, $xmlJsonId, $name_, $equipment_category_json_id, $isActive = 1)
    {
        parent::__construct($ID, $xmlId, $xmlJsonId, $isActive);
        $this->name = $name_;
        $this->equipment_category_json_id = intval($equipment_category_json_id);
    }
    /**
     * 
     * Additional functions 
     */
    public function getCategoryId (){
        return $this->equipment_category_json_id;
    }
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
                $object->equipment_category_json_id = $this->equipment_category_json_id;
                return $object->save();
            }
        }
        return false;
    }

}

?>