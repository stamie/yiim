<?php
namespace app\classes\yachtModel;
use app\classes\yachtModel\YachtModelSync;
use app\classes\Nausys;
use app\models\YachtModel;
use app\models\Xml;
class NausysYachtModel  extends YachtModelSync {
    private static $resturl = 'http://ws.nausys.com/CBMS-external/rest/catalogue/v6/yachtModels';
    private static $modelName = 'app\classes\yachtModel\NausysYachtModel';
    private static $model = 'app\models\YachtModel';
    private static $objectname = 'models'; //JSON-ban a második paraméter, a státusz után....
    public function __construct($ID = null, $xmlJsonId, $name_, $isActive = 1,
        $category_xml_id_,
        $builder_xml_id_,
        $loa,
        $beam,
        $draft,
        $cabins,
        $wc,
        $water_tank,
        $fuel_tank,
        $displacemen
    )
    {
        $xmlId = 0;
        $xml = Xml::findOne(array('slug' => 'nausys'));
        if ($xml){
            $xmlId = $xml->id;
        }
        parent::__construct($ID, $xmlId, $xmlJsonId, $name_, $isActive,
            $category_xml_id_,
            $builder_xml_id_,
            $loa,
            $beam,
            $draft,
            $cabins,
            $wc,
            $water_tank,
            $fuel_tank,
            $displacemen
        );
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
                self::inactiveRows(intval($xmlId));
                $return = true;
                foreach ($objectes as $obj) {
                    $objObj = new self::$modelName( null, $xml->id, 
                    intval($obj->id), $obj->name, 1, // ->textEN ); //,0 1 );
                                                    $obj->yachtCategoryId,
                                                    $obj->yachtBuilderId,
                                                    $obj->loa,
                                                    $obj->beam,
                                                    $obj->draft,
                                                    $obj->cabins,
                                                    $obj->wc,
                                                    isset($obj->waterTank)?intval($obj->waterTank):0,
                                                    isset($obj->fuelTank)?intval($obj->fuelTank):0,
                                                    $obj->displacement
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
    private static function inactiveRows(int $xml_id) {
        $objName = self::$model;
        $objectes = $objName::findAll(['xml_id' => $xml_id]);
        foreach ($objectes as $obj)
        {
            $obj->is_active = 0;
            $obj->save(false);
        }
        return true;
    }
}