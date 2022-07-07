<?php

namespace app\classes\equipmentCategory;

use app\models\EquipmentCategory as Model;

class EquipmentCategorySync {
    private static $model = 'app\models\EquipmentCategory';
    protected $id;

    protected $xml_id;
    protected $xml_json_id;

    protected $name;
    protected $is_active;

    public function __construct($ID = null, $wpId = 0, $wpPrefix, $xmlId, $xmlJsonId, $name_, $isActive = 1)
    {
        $this->id = $ID;
        
        
        $this->xml_id = $xmlId;
        $this->xml_json_id = $xmlJsonId;
        $this->name = $name_;
        $this->is_active = intval($isActive);

    }

    public function getId (){
        return $this->id;
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
    public function getName (){
        return $this->name;
    }
    public function getIsActive (){
        return $this->is_active;
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
                $object->save();
                
                return $object->save();
            } else {
                $object = new self::$model();
                
                
                
                $object->xml_id = $this->xml_id;
                $object->xml_json_id = $this->xml_json_id;
                $object->name = $this->name;
                $object->is_active = 1;
                return $object->save();
                
            }

            return false;
        }

        return false;
    }

}

?>