<?php

namespace app\classes\yacht;
use app\models\YachtDatas1;
use app\models\YachtDatas2;
use app\models\YachtDatas3;
use app\classes\Sync;

class YachtSync extends Sync{
    private static $model = 'app\models\Yacht';
    protected $name;
    protected $company_id;
    protected $base_id;
    protected $location_id;
    protected $yacht_model_id;
    protected $draft;
    protected $cabins;
    protected $cabins_crew;
    protected $berths_cabin;
    protected $berths_salon;
    protected $berths_crew;
    protected $berths_total;
    protected $wc;
    protected $wc_crew;
    protected $build_year;
    protected $engines;
    protected $engine_power;
    protected $steering_type_id;
    protected $sail_type_id;
    protected $sail_renewed;
    protected $genoa_type_id;
    protected $genoa_renewed;
    protected $commission;
    protected $deposit;
    protected $max_discount;
    protected $four_star_charter;
    protected $charter_type;
    protected $propulsion_type;
        protected $internal_use;
        protected $launched_year;
        protected $needs_option_approval;
        protected $can_make_booking_fixed;
        protected $fuel_tank;
        protected $water_tank;
        protected $mast_length;
        protected $number_of_rudder_blades;
        protected $engine_builder_id;
        protected $hull_color;
        protected $third_party_insurance_amount;
        protected $third_party_insurance_currency;
        protected $max_person;
        protected $wp_name;
    /**
     * 
     * Base functions 
     */
    public function __construct($ID = null, $xml_id, $xmlJson_id, $name_, $isActive = 1,
    $company_id,
    $base_id,
    $location_id,
    $yacht_model_id,
    $draft,
    $cabins,
    $cabins_crew,
    $berths_cabin,
    $berths_salon,
    $berths_crew,
    $berths_total,
    $wc,
    $wc_crew,
    $build_year,
    $engines,
    $engine_power,
    $steering_type_id,
    $sail_type_id,
    $sail_renewed,
    $genoa_type_id,
    $genoa_renewed,
    $commission,
    $deposit,
    $max_discount,
    $four_star_charter,
    $charter_type,
    $propulsion_type,
    $internal_use,
    $launched_year,
    $needs_option_approval,
    $can_make_booking_fixed,
    $fuel_tank,
    $water_tank,
    $mast_length,
    $number_of_rudder_blades,
    $engine_builder_id,
    $hull_color,
    $third_party_insurance_amount,
    $third_party_insurance_currency,
    $max_person
    ) {
        parent::__construct($ID, $xml_id, $xmlJson_id, $isActive);
        $this->name = $name_;
        $this->company_id = $company_id;
        $this->base_id = $base_id;
        $this->location_id = $location_id;
        $this->yacht_model_id = $yacht_model_id;
        $this->draft = $draft;
        $this->cabins = $cabins;
        $this->cabins_crew = $cabins_crew;
        $this->berths_cabin = $berths_cabin;
        $this->berths_salon = $berths_salon;
        $this->berths_crew = $berths_crew;
        $this->berths_total = $berths_total;
        $this->wc = $wc;
        $this->wc_crew = $wc_crew;
        $this->build_year = $build_year;
        $this->engines = $engines;
        $this->engine_power = $engine_power;
        $this->steering_type_id = $steering_type_id;
        $this->sail_type_id = $sail_type_id;
        $this->sail_renewed = $sail_renewed;
        $this->genoa_type_id = $genoa_type_id;
        $this->genoa_renewed = $genoa_renewed;
        $this->commission = $commission;
        $this->deposit = $deposit;
        $this->max_discount = $max_discount;
        $this->four_star_charter = isset($four_star_charter)?$four_star_charter:0;
        $this->charter_type = $charter_type;
        $this->propulsion_type = $propulsion_type;
        $this->internal_use = $internal_use;
        $this->launched_year = $launched_year;
        $this->needs_option_approval = $needs_option_approval;
        $this->can_make_booking_fixed = $can_make_booking_fixed;
        $this->fuel_tank = $fuel_tank;
        $this->water_tank = $water_tank;
        $this->mast_length = $mast_length;
        $this->number_of_rudder_blades = $number_of_rudder_blades;
        $this->engine_builder_id = $engine_builder_id;
        $this->hull_color = $hull_color;
        $this->third_party_insurance_amount = $third_party_insurance_amount;
        $this->third_party_insurance_currency =$third_party_insurance_currency;
        $this->max_person = $max_person;
    }
    /**
     * 
     * _syncrons function
     */
    public function sync () {
        if ($this) {
            $condition = [
                'xml_id' => $this->xml_id,
                'xml_json_id' => $this->xml_json_id,
                'is_archive' => 0,
                'is_active' => 0,
            ];
            $object = self::$model::findOne($condition);
            if (!$object){
                $object = new self::$model();
                $object->xml_id = $this->xml_id;
                $object->xml_json_id = $this->xml_json_id;
                $object->is_active = 1;
                $object->is_new = 1;
            }
            if ($object && (empty($object->id) || $object->id>3235)){
                $object->name = $this->name;
                $object->is_active = 1;
                $object->company_id = $this->company_id;
                $object->base_id = $this->base_id;
                $object->location_id = $this->location_id;
                $object->yacht_model_id = $this->yacht_model_id;
                $object->build_year = $this->build_year;
                $object->engine_builder_id = $this->engine_builder_id;
                $object->propulsion_type = $this->propulsion_type;
                $object->fuel_tank = $this->fuel_tank;
                $object->water_tank = $this->water_tank;
                $object->mast_length = $this->mast_length;
                $object->number_of_rudder_blades = $this->number_of_rudder_blades;
                $object->hull_color = $this->hull_color;
                $object->third_party_insurance_amount = $this->third_party_insurance_amount;
                $object->third_party_insurance_currency =$this->third_party_insurance_currency;
                $object->max_person = $this->max_person; $object->save(0);
                if ($object->id && $object->wp_name)
                    $object->save(0);
                else
                    $object->saveYacht();
                $object1 = YachtDatas1::findOne($object->id);
                if (!$object1){
                    $object1 = new YachtDatas1();
                }
                $object1->id = $object->id;
                $object1->xml_id = $this->xml_id;
                $object1->yacht_model_id = $this->yacht_model_id;
                $object1->draft = $this->draft;
                $object1->cabins = $this->cabins;
                $object1->cabins_crew = $this->cabins_crew;
                $object1->berths_cabin = $this->berths_cabin;
                $object1->berths_salon = $this->berths_salon;
                $object1->berths_crew = $this->berths_crew;
                $object1->berths_total = $this->berths_total;
                $object1->wc = $this->wc;
                $object1->wc_crew = $this->wc_crew; 
                $object1->save(0);
                $object2 = YachtDatas2::findOne($object->id);
                if (!$object2){
                    $object2 = new YachtDatas2();
                }
                $object2->id = $object->id;
                $object2->xml_id = $object->xml_id;
                $object2->engine_builder_id = $this->engine_builder_id;
                $object2->engines = $this->engines;
                $object2->engine_power = $this->engine_power;
                $object2->steering_type_id = $this->steering_type_id;
                $object2->sail_type_id = $this->sail_type_id;
                $object2->sail_renewed = $this->sail_renewed;
                $object2->genoa_type_id = $this->genoa_type_id;
                $object2->genoa_renewed = $this->genoa_renewed;
                $object2->save(0);
                $object3 = YachtDatas3::findOne($object->id);
                if (!$object3){
                    $object3 = new YachtDatas3();
                }
                $object3->id = $object->id;
                $object3->xml_id = $object->xml_id;
                $object3->xml_json_id = $object->xml_json_id;
                $object3->commission = $this->commission;
                $object3->deposit = $this->deposit;
                $object3->max_discount = $this->max_discount;
                $object3->four_star_charter = $this->four_star_charter;
                $object3->internal_use = $this->internal_use;
                $object3->launched_year = $this->launched_year;
                $object3->needs_option_approval = $this->needs_option_approval;
                $object3->can_make_booking_fixed = $this->can_make_booking_fixed;
                $object3->charter_type = $this->charter_type;
                $object3->save(0);
                return $object->id;
            }
        }
        return false;
    }
    protected static function rrmdir($dir) { 
        if (is_dir($dir)) { 
            $objects = scandir($dir);
            foreach ($objects as $object) { 
            if ($object != "." && $object != "..") { 
                if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir."/".$object))
                self::rrmdir($dir. DIRECTORY_SEPARATOR .$object);
                else
                @unlink($dir. DIRECTORY_SEPARATOR .$object); 
            } 
            }
            @rmdir($dir); 
        } 
    }
}