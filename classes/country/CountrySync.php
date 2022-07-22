<?php
namespace app\classes\country;
use app\models\Country;
use app\classes\Sync;
class CountrySync extends Sync{
    public $code;
    public $name;
    public function __construct($ID = null, $xmlId, $xmlJsonId, $code_, $name_, $isActive = 1)
    {
        parent::__construct($ID, $xmlId, $xmlJsonId, $isActive);
        $this->code = $code_;
        $this->name = $name_;
    }
    public function syncCountry () {
        if ($this) {
            $condition = [
                'xml_id' => $this->xml_id,
                'xml_json_id' => $this->xml_json_id,
            ];
            $country = Country::findOne($condition);
            if ($country){
                $country->is_active = 1;
                $country->save(0);
                return 1;
            } else {
                $country = new Country();
                $country->xml_id = $this->xml_id;
                $country->xml_json_id = $this->xml_json_id;
                $country->code = $this->code;
                $country->name = $this->name;
                $country->is_active = 1;
                if($country->save())
                return 1;
            }
        }
        return false;
    }
}
?>