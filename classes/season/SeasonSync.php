<?php

namespace app\classes\season;

use app\models\SeasonType;

class SeasonSync {
    private static $model = 'app\models\Season';
    
    protected $id;
    //protected $wp_id;
    protected $wp_prefix;
    protected $xml_id;
    protected $xml_json_id;
    protected $is_active;

    protected $season;
    protected $charter_company_id;
    protected $date_from;
    protected $date_to;
    protected $deafult_season;

    /**
     * 
     * Base functions 
     */

    public function __construct($ID = null, $wpId = 0, $wpPrefix, $xmlId, $xmlJsonId, $name_, $isActive = 1,
        $charter_company_id,
        $date_from,
        $date_to,
        $deafult_season = 1
    ) {
        $this->id = $ID;
        $this->xml_id = $xmlId;
        $this->xml_json_id = $xmlJsonId;
        $this->season = $name_;
        $this->is_active = intval($isActive);
        $this->charter_company_id = $charter_company_id;
        $this->date_from = $date_from;
        $this->date_to = $date_to;
        $this->deafult_season = $deafult_season;
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
        return $this->season;
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
                $object->season = $this->season;
                $object->charter_company_id = $this->charter_company_id;
                $object->date_from = $this->date_from;
                $object->date_to = $this->date_to;
                $object->deafult_season = $this->deafult_season;
                $object->is_active = 1;
                if ($object->save()){
                    return $object;
                }

                return null;
                
            }
        }
        return false;
    }
}

?>