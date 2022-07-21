<?php
namespace app\classes\country;

use app\classes\country\CountrySync;
use app\classes\Nausys;
use app\models\Country;
use app\models\Xml;

class NausysCountry  extends CountrySync {

    private static $modelName = 'app\classes\country\NausysCountry';
    private static $resturl   = 'http://ws.nausys.com/CBMS-external/rest/catalogue/v6/countries';
    public $xml_id;
    public $xml_json_id;
    public $code;
    public $name;
    public $is_active;

    public function __construct($ID = null, $xmlJsonId, $code_, $name_, $isActive = true)
    {
        $xmlId = 0;

        $xml = Xml::findOne(array('slug' => 'nausys'));

        if ($xml){
            $xmlId = $xml->id;
        }

        parent::__construct($ID, $xmlId, $xmlJsonId, $code_, $name_, $isActive);
        //$obj = new CountrySync($ID, $wpId, $wpPrefix, $xmlId, $xmlJsonId, $code_, $name_, $isActive);
        //var_dump($this); exit;

    }

    public static function syncronise() {

        $cred = new Nausys();

        $xmlId = 0;

        $xml = Xml::findOne(array('slug' => 'nausys'));

        if ($xml){
            $xmlId = intval($xml->id);
        }
        
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

                $obectes = $obj->countries;
                
                self::inactiveCountries(intval($prId), $xmlId);
                $return = true;

                foreach ($obectes as $obect) {

                    $obectObj = new self::$modelName( null, 0, $prId, intval($obect->id), $obect->code, $obect->name->textEN, true );
                    $return = $return && $obectObj->syncCountry();
                    
                }

                return $return;

            }
        }

        
        return false;
    }

    private static function inactiveCountries(int $prId, int $xmlId) {

        $obectes = Country::findAll([ 'xml_id' => $xmlId]);
        foreach ($obectes as $obect)
        {
            $obect->is_active = 0;
            $obect->save(false);

        }

        return true;
    }
}

?>