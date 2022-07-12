<?php

namespace app\classes\steeringtype;

use app\models\SteeringType;

class SteeringTypeSync {
    private static $model = 'app\models\SteeringType';
    
    protected $id;
    protected $wp_id;
    protected $wp_prefix;
    protected $xml_id;
    protected $xml_json_id;
    protected $name;
    protected $is_active;

   
    /**
     * 
     * Base functions 
     */

    public function __construct($ID = null, $wpId = 0, $wpPrefix, $xmlId, $xmlJsonId, $name_, $isActive = 1
    ) {

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