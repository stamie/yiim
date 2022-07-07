<?php

namespace app\classes\yachtModel;

use app\models\YachtModel;

class YachtModelSync {
    private static $model = 'app\models\YachtModel';
    
    protected $id;
    protected $wp_id;
    protected $wp_prefix;
    protected $xml_id;
    protected $xml_json_id;
    protected $name;
    protected $is_active;


    protected $category_xml_id;
    protected $builder_xml_id;
    protected $loa;
    protected $beam;
    protected $draft;
    protected $cabins;
    protected $wc;
    protected $water_tank;
    protected $fuel_tank;
    protected $displacemen;

    /**
     * 
     * Base functions 
     */

    public function __construct($ID = null, $wpId = 0, $wpPrefix, $xmlId, $xmlJsonId, $name_, $isActive = 1,
        $category_xml_id_,
        $builder_xml_id_,
        $loa,
        $beam,
        $draft,
        $cabins,
        $wc,
        $water_tank,
        $fuel_tank,
        $displacemen
    )
    {
        $this->id = $ID;
        
        
        $this->xml_id = $xmlId;
        $this->xml_json_id = $xmlJsonId;
        $this->name = $name_;
        $this->is_active = intval($isActive);


        $this->category_xml_id = $category_xml_id_;
        $this->builder_xml_id = $builder_xml_id_;
        $this->loa = $loa;
        $this->beam = $beam;
        $this->draft = $draft;
        $this->cabins = $cabins;
        $this->wc = $wc;
        $this->water_tank = $water_tank;
        $this->fuel_tank = $fuel_tank;
        $this->displacemen = $displacemen;
        

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
    public function getName (){
        return $this->name;
    }
    public function getIsActive (){
        return $this->is_active;
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

            $this->condition = [
                
                'xml_id' => $this->xml_id,
                'xml_json_id' => $this->xml_json_id,

            ];

            $object = self::$model::findOne($this->condition);
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

                $object->category_xml_id = $this->category_xml_id;
                $object->builder_xml_id = $this->builder_xml_id;
                $object->loa = $this->loa;
                $object->beam = $this->beam;
                $object->draft = $this->draft;
                $object->cabins = $this->cabins;
                $object->wc = $this->wc;
                $object->water_tank = $this->water_tank;
                $object->fuel_tank = $this->fuel_tank;
                $object->displacement = $this->displacemen;

                return $object->save();
                
            }

            return false;
        }

        return false;
    }

}

?>