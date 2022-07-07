<?php
namespace app\classes\season;

use app\classes\season\SeasonSync;
use app\classes\Nausys;
use app\models\Xml;


class NausysSeason  extends SeasonSync {

    private static $resturl = 'http://ws.nausys.com/CBMS-external/rest/catalogue/v6/seasons';
    private static $modelName = 'app\classes\season\NausysSeason';
    private static $model = 'app\models\Season';
    private static $objectname = 'seasons'; //JSON-ban a második paraméter, a státusz után....

    public function __construct($ID = null, $wpId = 0, $wpPrefix, $xmlJsonId, $name_, $isActive = 1,
        $charter_company_id,
        $date_from,
        $date_to,
        int $deafult_season
        )
    {
        $xmlId = 0;

        $xml = Xml::findOne(array('slug' => 'nausys'));

        if ($xml){
            $xmlId = $xml->id;
        }

        parent::__construct($ID, $wpId, $wpPrefix, $xmlId, $xmlJsonId, $name_, $isActive,
        $charter_company_id,
        $date_from,
        $date_to,
        $deafult_season
    );
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
            //var_dump($obj); exit;
            
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

                foreach ($objectes as $obj2) {
//var_dump($obj); exit;
                    $objObj = new self::$modelName( null, 0, $prId, intval($obj2->id), $obj2->season, 1 ,
                        $obj2->charterCompanyId,
                        date('Y-m-d', strtotime($obj2->from)),
                        date('Y-m-d', strtotime($obj2->to)),
                        $obj2->defaultSeason?1:0
                    );
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