<?php
namespace app\classes\equipmentCategory;

use app\classes\equipmentCategory\EquipmentCategorySync;
use app\classes\Nausys;
use app\models\EquipmentCategory;
use app\models\Xml;

class NausysEquipmentCategory  extends EquipmentCategorySync {

    private static $resturl = 'http://ws.nausys.com/CBMS-external/rest/catalogue/v6/equipmentCategories';
    private static $modelName = 'app\classes\equipmentCategory\NausysEquipmentCategory';
    private static $objectname = 'equipmentCategories';

    public function __construct($ID = null, $xmlJsonId, $name_, $isActive = 1)
    {
        $xmlId = 0;
        $xml = Xml::findOne(array('slug' => 'nausys'));
        if ($xml){
            $xmlId = $xml->id;
        }
        parent::__construct($ID, $xmlId, $xmlJsonId, $name_, $isActive);
    }
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
    private static function inactiveRows( int $xml_id) {
        $objectes = EquipmentCategory::findAll(['xml_id' => $xml_id]);
        foreach ($objectes as $obj)
        {
            $obj->is_active = 0;
            $obj->save(false);
        }
        return true;
    }
}
?>