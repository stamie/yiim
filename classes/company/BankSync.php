<?php

namespace app\classes\company;

use app\models\Bank;

class BankSync {
    private static $model = 'app\models\Bank';
    
    protected $id;
    protected $wp_id;
    protected $wp_prefix;
    protected $xml_id;
    protected $xml_json_id;
    protected $name;
    protected $is_active;


    protected $company_id;
    protected $bank_name;
    protected $bank_address;
    protected $account_number;
    protected $swift;
    protected $iban;

    // bankAccounts

    /**
     * 
     * Base functions 
     */

    public function __construct($ID = null, $xml_id,
                                $company_id,
                                $bankName,
                                $bankAddress,
                                $accountNumber,
                                $swift = null,
                                $iban = null
    ) {
        $this->xml_id = $xml_id;
        $this->company_id = $company_id;
        $this->bank_name = $bankName;
        $this->bank_address = $bankAddress;
        $this->account_number = $accountNumber;
        $this->swift = $swift;
        $this->iban = $iban;

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
                'company_id' => $this->company_id,
                'account_number' => $this->account_number,

            ];

            $object = self::$model::findOne($condition);
            if ($object){
                $object->is_active = 1;
                //$object->save();
                
                return $object->save(0);
            } else {
                $object = new self::$model();
                
                
                $object->xml_id = $this->xml_id;
                $object->is_active = 1;
                $object->company_id = $this->company_id;
                $object->bank_name = $this->bank_name;
                $object->bank_address = $this->bank_address;
                $object->account_number = $this->account_number;
                $object->swift = $this->swift;
                $object->iban = $this->iban;
                return $object->save();
                
            }


        }

        return false;
    }

}

?>