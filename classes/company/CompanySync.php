<?php

namespace app\classes\company;

use app\models\CompanyType;


class CompanySync {
    private static $model = 'app\models\Company';
    
    protected $id;
    protected $wp_id;
    protected $wp_prefix;
    protected $xml_id;
    protected $xml_json_id;
    protected $name;
    protected $is_active;

    protected $address;
    protected $city;
    protected $zip;
    protected $country_id;
    protected $phone;
    protected $fax;
    protected $mobile;
    protected $vatcode;
    protected $web;
    protected $email;
    protected $pac;
    // bankAccounts

    /**
     * 
     * Base functions 
     */

    public function __construct($ID = null, $wpId = 0, $wpPrefix, $xmlId, $xmlJsonId, $name_, $isActive = 1,
    $address,
    $city,
    $zip,
    $countryId,
    $phone,
    $fax = null,
    $mobile = null,
    $vatcode,
    $web = null,
    $email,
    $pac
    ) {

        $this->id = $ID;
        
        
        $this->xml_id = $xmlId;
        $this->xml_json_id = $xmlJsonId;
        $this->name = $name_;
        $this->is_active = intval($isActive);

        $this->address = $address;
        $this->city = $city;
        $this->zip = $zip;
        $this->country_id = intval($countryId);
        $this->phone = $phone;
        $this->fax = $fax;
        $this->mobile = $mobile;
        $this->vatcode = $vatcode;
        $this->web = $web;
        $this->email = $email;
        $this->pac = $pac;
        
 

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
                
                
                return $object->id;
            } else {
                $object = new self::$model();
                
                
                
                $object->xml_id = $this->xml_id;
                $object->xml_json_id = $this->xml_json_id;
                $object->name = $this->name;
                $object->is_active = 1;

                $object->address = $this->address;
                $object->city = $this->city;
                $object->zip = $this->zip;
                $object->country_id = intval($this->country_id);
                $object->phone = $this->phone;
                $object->fax = $this->fax;
                $object->mobile = $this->mobile;
                $object->vatcode = $this->vatcode;
                $object->web = $this->web;
                $object->email = $this->email;
                $object->pac = $this->pac;

                
                
                $object->save();

                return $object->id;
                
            }

           
        }

        return false;
    }

}

?>