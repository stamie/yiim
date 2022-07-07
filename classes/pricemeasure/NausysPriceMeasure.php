<?php
namespace app\classes\pricemeasure;

use app\classes\pricemeasure\PriceMeasureSync;
use app\classes\Nausys;
use app\models\PriceMeasure;
use app\models\Xml;

class NausysPriceMeasure  extends PriceMeasureSync {

    private static $resturl = 'http://ws.nausys.com/CBMS-external/rest/catalogue/v6/priceMeasures';
    private static $modelName = 'app\classes\pricemeasure\NausysPriceMeasure';
    private static $model = 'app\models\PriceMeasure';
    private static $objectname = 'priceMeasures'; //JSON-ban a második paraméter, a státusz után....



    public function __construct($ID = null, $wpId = 0, $wpPrefix, $xmlJsonId, $isActive = 1, 
                                $name
                                )
    {
        $xmlId = 0;

        $xml = Xml::findOne(array('slug' => 'nausys'));

        if ($xml){
            $xmlId = $xml->id;
        }

        parent::__construct($ID, $wpId, $wpPrefix, $xmlId, $xmlJsonId, $isActive, 
                                $name
                            );
      

    }

    /**
     * 
     * Syncrons function
     */

    public static function syncronise($prId) {

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
                self::inactiveRows(intval($prId), intval($xmlId) );

                $return = true;

                foreach ($objectes as $obj) {
                    
                    $objObj = new self::$modelName( null, 0, $prId, intval($obj->id), 1, 
                                                   $obj->name->textEN);
                    $return = $return && $objObj->sync();
                    
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
    private static function inactiveRows($prId, $xml_id) {

        $objName = self::$model;

        $objectes = $objName::findAll([ 'xml_id' => $xml_id]);
        foreach ($objectes as $obj)
        {
            $obj->is_active = 0;
            $obj->save(false);

        }

        return true;
    }
}

?>