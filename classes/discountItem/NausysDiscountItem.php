<?php
namespace app\classes\discountItem;

use app\classes\discountItem\DiscountItemSync;
use app\classes\Nausys;
use app\models\DiscountItem;
use app\models\Xml;

class NausysDiscountItem  extends DiscountItemSync {

    private static $resturl = 'http://ws.nausys.com/CBMS-external/rest/catalogue/v6/discountItems';
    private static $modelName = 'app\classes\discountItem\NausysDiscountItem';
    private static $model = 'app\models\DiscountItem';
    private static $objectname = 'discounts'; //JSON-ban a második paraméter, a státusz után....

    public function __construct($ID = null, $xmlJsonId, $name_, $isActive = 1)
    {
        $xmlId = 0;

        $xml = Xml::findOne(array('slug' => 'nausys'));

        if ($xml){
            $xmlId = $xml->id;
        }

        parent::__construct($ID, $xmlId, $xmlJsonId, $name_, $isActive);
        //$obj = new CountrySync($ID, $wpId, $wpPrefix, $xmlId, $xmlJsonId, $name_, $isActive);
        //var_dump($this); exit;

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
                self::inactiveRows( intval($xmlId) );
                $return = true;

                foreach ($objectes as $obj) {

                    $objObj = new self::$modelName( null, intval($obj->id), $obj->name->textEN, 1 );
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
    private static function inactiveRows( int $xml_id) {

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