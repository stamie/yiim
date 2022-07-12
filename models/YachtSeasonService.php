<?php
namespace app\models;
use Yii;
/**
 * This is the model class for table "yacht_season_service".
 * @property int $id
 * @property int|null $wp_id
 * @property int $xml_id
 * @property int $xml_json_id
 * @property string $name
 * @property int $is_active
 * @property int $season_id
 * @property int $service_id
 * @property string $price
 * @property string $currency
 * @property int $price_measure_id
 * @property string $calculation_type
 * @property string $description
 * @property int $have_valid_for_bases
 * @property int $obligatory
 * @property string $amount
 * @property int $amount_is_percentage
 * @property string $percentage_calculation_type
 * @property string $valid_period_from
 * @property string $valid_period_to
 * @property int $valid_min_pax
 * @property int $valid_max_pax
 * @property string $minimum_price
 */
class YachtSeasonService extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'yacht_season_service';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['xml_id', 'xml_json_id', 'is_active', 'yacht_id', 'season_id', 'service_id', 'price_measure_id', 'have_valid_for_bases', 'obligatory', 'amount_is_percentage', 'valid_min_pax', 'valid_max_pax'], 'integer'],
            [['xml_id', 'xml_json_id', 'season_id', 'yacht_id', 'service_id', 'price', 'currency', 'price_measure_id', 'calculation_type', 'obligatory', 'amount'], 'required'],
            [['description'], 'string'],
            [['price', 'minimum_price'], 'string', 'max' => 20],
            [['currency'], 'string', 'max' => 5],
            [['calculation_type', 'amount', 'percentage_calculation_type', 'valid_period_from', 'valid_period_to'], 'string', 'max' => 50],
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
            'xml_id' => 'Xml ID',
            'xml_json_id' => 'Xml Json ID',
            'name' => 'Name',
            'is_active' => 'Is Active',
            'season_id' => 'Season ID',
            'service_id' => 'Service ID',
            'price' => 'Price',
            'currency' => 'Currency',
            'price_measure_id' => 'Price Measure ID',
            'calculation_type' => 'Calculation Type',
            'description' => 'Description',
            'have_valid_for_bases' => 'Have Valid For Bases',
            'obligatory' => 'Obligatory',
            'amount' => 'Amount',
            'amount_is_percentage' => 'Amount Is Percentage',
            'percentage_calculation_type' => 'Percentage Calculation Type',
            'valid_period_from' => 'Valid Period From',
            'valid_period_to' => 'Valid Period To',
            'valid_min_pax' => 'Valid Min Pax',
            'valid_max_pax' => 'Valid Max Pax',
            'minimum_price' => 'Minimum Price',
        ];
    }

    public static function sync(
        $xml_id,
        $xml_json_id,
        $yacht_id,
        $season_id,
        $service_id,
        $price,
        $currency,
        $price_measure_id,
        $calculation_type = '',
        $obligatory = 0,
        $description = '',
        $amount,
        $have_valid_for_bases = 0,
        $amount_is_percentage = null,
        $percentage_calculation_type = null,
        $valid_period_from = null,
        $valid_period_to = null,
        $valid_min_pax = null,
        $valid_max_pax = null,
        $minimum_price = null

    ) {

        $yachtSeasonService = YachtSeasonService::findOne([
            'xml_id' => $xml_id,
            'yacht_id' => $yacht_id,
            'season_id' => $season_id,
            'service_id' => intval($service_id),
            'xml_json_id' => $xml_json_id

        ]);
        if ($yachtSeasonService) {

            $yachtSeasonService->is_active = 1;
            $yachtSeasonService->save(0);
            // var_dump($yachtSeasonService);exit ("vok");
            return $yachtSeasonService;
        }

        $yachtSeasonService = new YachtSeasonService();

        $yachtSeasonService->xml_id = $xml_id;
        $yachtSeasonService->xml_json_id = $xml_json_id;
        $yachtSeasonService->season_id = $season_id;

        $yachtSeasonService->yacht_id = $yacht_id;
        $yachtSeasonService->service_id = intval($service_id);
        $yachtSeasonService->price = $price;
        $yachtSeasonService->currency = $currency;
        $yachtSeasonService->price_measure_id = intval($price_measure_id);
        $yachtSeasonService->calculation_type = $calculation_type;
        $yachtSeasonService->obligatory = $obligatory;
        $yachtSeasonService->amount = $amount;

        $yachtSeasonService->description = $description;
        $yachtSeasonService->have_valid_for_bases = $have_valid_for_bases;
        $yachtSeasonService->amount_is_percentage = $amount_is_percentage;
        $yachtSeasonService->percentage_calculation_type = $percentage_calculation_type;
        $yachtSeasonService->valid_period_from = $valid_period_from;
        $yachtSeasonService->valid_period_to = $valid_period_to;
        $yachtSeasonService->valid_min_pax = $valid_min_pax;
        $yachtSeasonService->valid_max_pax = $valid_max_pax;
        $yachtSeasonService->minimum_price = $minimum_price;
        if ($yachtSeasonService->save()) {

            return $yachtSeasonService;
        }
        return 0;
    }

    public static function inactiveAll(
        $yacht_id,
        $xml_id

    ) {
        $yachtSeasonService = YachtSeasonService::findOne([
            'yacht_id' => $yacht_id,
            'xml_id' => $xml_id,
            'is_active' => 1,
        ]);

        while ($yachtSeasonService) {


            $yachtSeasonService->is_active = 0;
            $yachtSeasonService->save(0);
            $yachtSeasonService = YachtSeasonService::findOne([
                'yacht_id' => $yacht_id,
                'xml_id' => $xml_id,
                'is_active' => 1,
            ]);
        }

        return;
    }

    public static function inactiveAllThisYacht($yacht_id) {
        $yachtSeasonService = YachtSeasonService::findOne([
            'yacht_id' => $yacht_id,
            'is_active' => 1,
        ]);

        while ($yachtSeasonService) {


            $yachtSeasonService->is_active = 0;
            $yachtSeasonService->save(0);
            $yachtSeasonService = YachtSeasonService::findOne([
                'yacht_id' => $yacht_id,
                'is_active' => 1,
            ]);
        }

        return;
    }
}
