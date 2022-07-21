<?php
namespace app\classes\yachtCategory;

use app\classes\yachtCategory\YachtCategorySync;
use app\classes\Nausys;
use app\models\YachtCategory;
use app\models\Xml;

class NausysYachtCategory  extends YachtCategorySync {

    private static $resturl = 'http://ws.nausys.com/CBMS-external/rest/catalogue/v6/yachtCategories';
    private static $modelName = 'app\classes\yachtCategory\NausysYachtCategory';
    private static $model = 'app\models\YachtCategory';
    private static $objectname = 'categories'; //JSON-ban a második paraméter, a státusz után....

    public function __construct($ID = null, $wpId = 0, $wpPrefix, $xmlJsonId, $name_, $isActive = 1)
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

                    $objObj = new self::$modelName( null, 0, intval($obj->id), $obj->name->textEN ); //, 1 );
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
    private static function inactiveRows(int $prId, int $xml_id) {

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