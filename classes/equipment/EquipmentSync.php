<?php

namespace app\classes\equipment;

use app\models\Equipment as Model;

class EquipmentSync {
    private static $model = 'app\models\Equipment';
    
    protected $id;
    protected $wp_id;
    protected $wp_prefix;
    protected $xml_id;
    protected $xml_json_id;
    protected $name;
    protected $is_active;
    protected $equipment_category_json_id;


    /**
     * 
     * Base functions 
     */

    public function __construct($ID = null, $wpId = 0, $wpPrefix, $xmlId, $xmlJsonId, $name_, $equipment_category_json_id, $isActive = 1)
    {
        $this->id = $ID;
        
        
        $this->xml_id = $xmlId;
        $this->xml_json_id = $xmlJsonId;
        $this->name = $name_;
        $this->is_active = intval($isActive);
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