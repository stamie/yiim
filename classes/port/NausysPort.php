<?php
namespace app\classes\port;

use app\classes\port\PortSync;
use app\classes\Nausys;
use app\models\Port;
use app\models\Xml;

class NausysPort  extends PortSync {

    private static $resturl = 'http://ws.nausys.com/CBMS-external/rest/catalogue/v6/locations';
    private static $modelName = 'app\classes\port\NausysPort';
    private static $model = 'app\models\Port';
    private static $objectname = 'locations'; //JSON-ban a második paraméter, a státusz után....


    public function __construct($ID = null, $xmlJsonId, $name_, $isActive = 1, $country_id, $lon = null, $lat=null, $wp_lon = null, $wp_lat = null)
    {
        $xmlId = 0;
        $xml = Xml::findOne(array('slug' => 'nausys'));
        if ($xml){
            $xmlId = $xml->id;
        }
        parent::__construct($ID, $xmlId, $xmlJsonId, $name_, $isActive, $country_id, $lon, $lat, $wp_lon, $wp_lat);
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
                self::inactiveRows(intval($prId), intval($xmlId) );
                $return = true;
                foreach ($objectes as $obj) {
                    $objObj = new self::$modelName( null, 0, $prId, intval($obj->id), $obj->name->textEN, 1, 
                                                    intval($obj->regionId),
                                                    $obj->lon,
                                                    $obj->lat);
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