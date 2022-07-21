<?php

namespace app\classes\company;

use app\models\Bank;
use app\classes\Sync;

class BankSync extends Sync {
    private static $model = 'app\models\Bank';
    protected $company_id;
    protected $bank_name;
    protected $bank_address;
    protected $account_number;
    protected $swift;
    protected $iban;
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
        parent::__construct($ID, $xml_id, 1, 1);
        $this->company_id = $company_id;
        $this->bank_name = $bankName;
        $this->bank_address = $bankAddress;
        $this->account_number = $accountNumber;
        $this->swift = $swift;
        $this->iban = $iban;
    }
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