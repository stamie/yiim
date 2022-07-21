<?php

namespace app\classes\equipment;

use app\classes\equipment\EquipmentSync;
use app\classes\Nausys;
use app\models\Xml;

class NausysEquipment  extends EquipmentSync
{

    private static $resturl = 'http://ws.nausys.com/CBMS-external/rest/catalogue/v6/equipment';
    private static $modelName = 'app\classes\equipment\NausysEquipment';
    private static $model = 'app\models\Equipment';
    private static $objectname = 'equipment';

    public function __construct($ID = null, $xmlJsonId, $name_, $equipment_category, $isActive = 1)
    {
        $xmlId = 0;
        $xml = Xml::findOne(array('slug' => 'nausys'));
        if ($xml) {
            $xmlId = $xml->id;
        }
        parent::__construct($ID, $xmlId, $xmlJsonId, $name_, $equipment_category, $isActive);
    }
    public static function syncronise($prId)
    {
        $cred = new Nausys();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$resturl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,  $cred->getJsonCredentials());
        $header = array('Content-Type: application/json');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $exec = curl_exec($ch);
        curl_close($ch);
        if ($exec) {
            $obj = json_decode($exec);
            if ($obj->status == "OK") {
                $objName = self::$objectname;
                $objectes = $obj->$objName;
                $xmlId = 0;
                $xml = Xml::findOne(array('slug' => 'nausys'));
                if ($xml) {
                    $xmlId = $xml->id;
                }
                self::inactiveRows( intval($xmlId));
                $return = true;
                foreach ($objectes as $obj2) {
                    $catId = isset($obj2->categoryId) ? intval($obj2->categoryId) : 0;
                    $objObj = new self::$modelName(null, 0, $prId, intval($obj2->id), $obj2->name->textEN,  $catId, 1);
                    $return = $return && $objObj->sync();
                }
                return $return;
            }
        }
        return false;
    }
    private static function inactiveRows( int $xml_id)
    {
        $objName = self::$model;
        $objectes = $objName::findAll(['xml_id' => $xml_id]);
        foreach ($objectes as $obj) {
            $obj->is_active = 0;
            $obj->save(false);
        }
        return true;
    }
}
