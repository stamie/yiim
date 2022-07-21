<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "yacht".
 *
 * @property int $id
 * @property int|null $wp_id
 
 * @property int $xml_id
 * @property int $xml_json_id
 * @property string $name
 * @property int $is_active
 * @property int $base_id
 * @property int $location_id
 * @property int $yacht_model_id
 * @property int $company_id
 * @property float $draft
 * @property int $cabins
 * @property int $cabins_crew
 * @property int $berths_cabin
 * @property int $berths_salon
 * @property int $berths_crew
 * @property int $berths_total
 * @property int $wc
 * @property int $wc_crew
 * @property int $build_year
 * @property int|null $engines
 * @property float|null $engine_power
 * @property int|null $steering_type_id
 * @property int|null $sail_type_id
 * @property int|null $sail_renewed
 * @property int|null $genoa_type_id
 * @property int|null $genoa_renewed
 * @property float $commission
 * @property float $deposit
 * @property float|null $max_discount
 * @property int $four_star_charter
 * @property int|null $internal_use
 * @property int|null $launched_year
 * @property int $needs_option_approval
 * @property int $can_make_booking_fixed
 * @property string|null $charter_type
 * @property int $fuel_tank
 * @property int $water_tank
 * @property float|null $mast_length
 * @property string|null $propulsion_type
 * @property int|null $number_of_rudder_blades
 * @property int|null $engine_builder_id
 * @property string|null $hull_color
 * @property float|null $third_party_insurance_amount
 * @property string|null $third_party_insurance_currency
 * @property string|null $wp_name
 */
class Yacht extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'yacht';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['wp_prefix', 'wp_id', 'xml_id', 'xml_json_id', 'is_active', 'is_archive', 'base_id', 'location_id', 'yacht_model_id', 'company_id', 'cabins', 'cabins_crew', 'berths_cabin', 'berths_salon', 'berths_crew', 'berths_total', 'wc', 'wc_crew', 'build_year', 'engines', 'steering_type_id', 'sail_type_id', 'sail_renewed', 'genoa_type_id', 'genoa_renewed', 'four_star_charter', 'internal_use', 'launched_year', 'needs_option_approval', 'can_make_booking_fixed', 'fuel_tank', 'water_tank', 'number_of_rudder_blades', 'engine_builder_id', 'max_person', 'is_new'], 'integer'],
            [['wp_prefix', 'xml_id', 'xml_json_id', 'name', 'base_id', 'location_id', 'yacht_model_id', 'company_id', 'draft', 'cabins', 'cabins_crew', 'berths_cabin', 'berths_salon', 'berths_crew', 'berths_total', 'wc', 'wc_crew', 'build_year', 'commission', 'deposit', 'needs_option_approval', 'can_make_booking_fixed', 'fuel_tank', 'water_tank'], 'required'],
            [['draft', 'engine_power', 'commission', 'deposit', 'max_discount', 'mast_length', 'third_party_insurance_amount'], 'number'],
            [['name'], 'string', 'max' => 100],
            [['charter_type', 'propulsion_type', 'hull_color', 'wp_name'], 'string', 'max' => 200],
            [['third_party_insurance_currency'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'wp_id' => 'Wp ID',
            'wp_prefix' => 'Wp Prefix',
            'xml_id' => 'Xml ID',
            'xml_json_id' => 'Xml Json ID',
            'name' => 'Name',
            'is_active' => 'Is Active',
            'is_archive' => 'Is Archive',
            'base_id' => 'Base ID',
            'location_id' => 'Location ID',
            'yacht_model_id' => 'Yacht Model ID',
            'company_id' => 'Company ID',
            'draft' => 'Draft',
            'cabins' => 'Cabins',
            'cabins_crew' => 'Cabins Crew',
            'berths_cabin' => 'Berths Cabin',
            'berths_salon' => 'Berths Salon',
            'berths_crew' => 'Berths Crew',
            'berths_total' => 'Berths Total',
            'wc' => 'Wc',
            'wc_crew' => 'Wc Crew',
            'build_year' => 'Build Year',
            'engines' => 'Engines',
            'engine_power' => 'Engine Power',
            'steering_type_id' => 'Steering Type ID',
            'sail_type_id' => 'Sail Type ID',
            'sail_renewed' => 'Sail Renewed',
            'genoa_type_id' => 'Genoa Type ID',
            'genoa_renewed' => 'Genoa Renewed',
            'commission' => 'Commission',
            'deposit' => 'Deposit',
            'max_discount' => 'Max Discount',
            'four_star_charter' => 'Four Star Charter',
            'internal_use' => 'Internal Use',
            'launched_year' => 'Launched Year',
            'needs_option_approval' => 'Needs Option Approval',
            'can_make_booking_fixed' => 'Can Make Booking Fixed',
            'charter_type' => 'Charter Type',
            'fuel_tank' => 'Fuel Tank',
            'water_tank' => 'Water Tank',
            'mast_length' => 'Mast Length',
            'propulsion_type' => 'Propulsion Type',
            'number_of_rudder_blades' => 'Number Of Rudder Blades',
            'engine_builder_id' => 'Engine Builder ID',
            'hull_color' => 'Hull Color',
            'third_party_insurance_amount' => 'Third Party Insurance Amount',
            'third_party_insurance_currency' => 'Third Party Insurance Currency',
            'wp_name' => 'Wp Name',
        ];
    }

    public function saveYacht()
    {
        if (isset($this->yacht_model_id)) {
            $yachtModel = YachtModel::findOne(['xml_json_id' => $this->yacht_model_id, 'xml_id' => $this->xml_id]);
            if (empty($yachtModel)) {
                $ch = curl_init(Yii::$app->params['baseurl'] . "yachtmodel?id={$this->xml_id}");
                $exec = curl_exec($ch);
                var_dump("szia exec");
                var_dump($exec);
                var_dump("szia exec");
                curl_close($ch);
                $yachtModel = YachtModel::findOne(['xml_json_id' => $this->yacht_model_id, 'xml_id' => $this->xml_id]);
                var_dump($this->yacht_model_id);
                var_dump($yachtModel);
            }
            $yachtName = 'boat1';
            if ($yachtModel) {
                $yachtName = str_replace([' ', '+', '.'], '-', strtolower($yachtModel->name));
                while (strpos($yachtName, '--')) {
                    $yachtName = str_replace('--', '-', $yachtName);
                }
            }
            $yachtName = trim($yachtName, '-');
            $yachtName = $yachtName . '-' . $this->build_year;
            $this->wp_name = $yachtName;
            $allYachts = Yacht::find()->where("wp_name like '$yachtName%'")->all();
            if (is_array($allYachts) && count($allYachts) > 0) {
                $number = count($allYachts);
                $this->wp_name = str_replace(['(', ')', ','], ['', '', ''], $yachtName) . '-' . $number;
            }
            $this->save(0);
            return $this;
        }
        return false;
    }

    public function refreshWpName()
    {

        if (empty($this->wp_name)) {

            $yachtModel = YachtModel::findOne(['xml_json_id' => $this->yacht_model_id]);
            if ($yachtModel) {
                $yachtName = str_replace(['/', ' ', '+', '.'], '-', strtolower($yachtModel->name));
                while (strpos($yachtName, '--')) {
                    $yachtName = str_replace('--', '-', $yachtName);
                }
                $yachtName = trim($yachtName, '-');

                $yachtName = $yachtName . '-' . $this->build_year;

                $this->wp_name = $yachtName;

                $allYachts = Yacht::find()->where("wp_name like '$yachtName%'")->all();

                if (is_array($allYachts) && count($allYachts) > 0) {

                    $number = count($allYachts);
                    $this->wp_name = $yachtName . '-' . $number;
                }

                $this->save(0);

                return $this;
            }
        }

        return false;
    }

    public function getPostTitle()
    {

        $yachtModel = YachtModel::findOne(['xml_json_id' => $this->yacht_model_id]);
        if ($yachtModel) {
            $yachtName = $yachtModel->name;
            while (strpos($yachtName, '--')) {
                $yachtName = str_replace('--', '-', $yachtName);
            }
            $yachtName = trim($yachtName, '-');

            $yachtName = $yachtName . ' (' . $this->build_year . ')';

            return $yachtName;
        }
        return '';
    }

    public static function archive()
    {
        $yacht = Yacht::findOne(['is_active' => 0, 'is_archive' => 0]);

        while ($yacht) {
            $yacht->is_archive = 1;
            $yacht->save(0);
            $yacht = Yacht::findOne(['is_active' => 0, 'is_archive' => 0]);
        }
    }
}
