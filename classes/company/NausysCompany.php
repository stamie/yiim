<?php
namespace app\classes\company;

use app\classes\company\CompanySync;
use app\classes\Nausys;
use app\models\Xml;
use app\classes\country\BankSync;


class NausysCompany  extends CompanySync {

    private static $resturl = 'http://ws.nausys.com/CBMS-external/rest/catalogue/v6/charterCompanies';
    private static $modelName = 'app\classes\company\NausysCompany';
    private static $model = 'app\models\Company';
    private static $objectname = 'companies'; //JSON-ban a második paraméter, a státusz után....
    private static $subModel = 'app\models\Bank';
    private static $subModelsName = 'app\classes\company\BankSync';

    public function __construct($ID = null, $xmlJsonId, $name_, $isActive = 1,
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
                                )
    {
        $xmlId = 0;

        $xml = Xml::findOne(array('slug' => 'nausys'));

        if ($xml){
            $xmlId = $xml->id;
        }

        parent::__construct($ID, $xmlId, $xmlJsonId, $name_, $isActive,
                            $address,
                            $city,
                            $zip,
                            $countryId,
                            $phone,
                            $fax,
                            $mobile,
                            $vatcode,
                            $web,
                            $email,
                            $pac
                            );
        //$obj = new CountrySync($ID, $wpId, $wpPrefix, $xmlId, $xmlJsonId, $name_, $isActive);
        //var_dump($this); exit;

    }

    /**
     * 
     * Syncrons function
     */

    public static function syncronise() {

        $cred = new Nausys();
        
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, self::$resturl );
        curl_setopt( $ch, CURLOPT_POST, true ); 
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS,  $cred->getJsonCredentials() ); 
           

        $header = array('Content-Type: application/json');
        

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $exec = curl_exec($ch);
        curl_close($ch);

        if( $exec ){

            $obj = json_decode ( $exec );
            
            if ($obj->status == "OK") {

                $objName = self::$objectname;

                $objectes = $obj->$objName;
                
                $xmlId = 0;

                $xml = Xml::findOne(array('slug' => 'nausys'));

                if ($xml){
                    $xmlId = $xml->id;
                }
                self::inactiveRows( intval($xmlId) );

                $return = true;

                foreach ($objectes as $obj) {

                    $objObj = new self::$modelName( null, 0, $prId, intval($obj->id), $obj->name, 1, //->textEN, 1, 
                                                    isset($obj->address)?$obj->address:'0',
                                                    isset($obj->city)?$obj->city:'0',
                                                    isset($obj->zip)?$obj->zip:'0',
                                                    isset($obj->countryId)?$obj->countryId:0,
                                                    isset($obj->phone)?$obj->phone:'0',
                                                    isset($obj->fax)?$obj->fax:null,
                                                    isset($obj->mobile)?$obj->mobile:null,
                                                    isset($obj->vatcode)?$obj->vatcode:'0',
                                                    isset($obj->web)?$obj->web:null,
                                                    $obj->email,
                                                    $obj->pac
                                                    );

                    $returnId = $objObj->sync();
                    $subReturn = true;
                    
                    if ($returnId && isset($obj->bankAccounts)){
                        foreach($obj->bankAccounts as $bank){
                            $subObjectsName = self::$subModelsName;
                            $subObject = new $subObjectsName ( null, 1, intval($prId), intval($xmlId) ,
                                                                $objObj->xml_json_id,                                    
                                                                $bank->bankName,
                                                                $bank->bankAddress,
                                                                isset($bank->accountNumber)?$bank->accountNumber:null,
                                                                isset($bank->swift)?$bank->swift:null,
                                                                isset($bank->iban)?$bank->iban:null
                                                              );
                            
                            $subReturn = $subReturn && $subObject->sync();
                        }
                    }

                    $return = $return && $returnId && $subReturn;
                    
                }

                return $return;

            }
        }

        
        return false;
    }

    /**
     * 
     * Inactive All rows function
     */
    private static function inactiveRows(int $prId, int $xml_id) {

        $objName = self::$model;

        $objectes = $objName::findAll([ 'xml_id' => $xml_id]);
        foreach ($objectes as $obj)
        {
            $subObjName = self::$subModel;
            $subObjects = $subObjName::findAll([ 'xml_id' => $xml_id, 'company_id' => $obj->xml_json_id]);
            
            if (is_array($subObjects)){
                foreach ($subObjects as $subObject) {
                    $subObject->is_active = 0;
                    $subObject->save(false);
                }
            }

            $obj->is_active = 0;
            $obj->save(false);

        }

        return true;
    }
}

?>