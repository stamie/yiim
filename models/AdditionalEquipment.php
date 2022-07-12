<?php
namespace app\models;
use Yii;
/**
 * This is the model class for table "additional_equipment".
 * @property int $id
 * @property int|null $wp_id
 * @property int $xml_json_id
 * @property int $xml_id
 * @property string $
 * @property int $is_active
 * @property int $quantity
 * @property float $price
 * @property string $currency
 * @property int $yacht_id
 * @property int $equipment_id
 * @property int $season_id
 * @property string $comment
 * @property int $price_measure_id
 * @property string $calculation_type
 * @property string $condition_string
 * @property float $amount
 * @property int $amount_is_percentage
 * @property string $percentage_calculation_type
 * @property float $minimum_price
 */
class AdditionalEquipment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'additional_equipment';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['xml_json_id', 'xml_json_id', 'xml_id', 'is_active', 'quantity', 'yacht_id', 'equipment_id', 'season_id', 'price_measure_id', 'amount_is_percentage'], 'integer'],
            [[ 'xml_id', 'quantity', 'price', 'currency', 'yacht_id', 'equipment_id', 'season_id', 'price_measure_id', 'calculation_type', 'condition_string', 'amount', 'amount_is_percentage', 'percentage_calculation_type', 'minimum_price'], 'required'],
            [['price', 'amount', 'minimum_price'], 'number'],
            [['comment', 'calculation_type', 'condition_string', 'percentage_calculation_type'], 'string', 'max' => 100],
            [['currency'], 'string', 'max' => 50],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'xml_json_id' => 'Xml Json ID',
            'xml_id' => 'Xml ID',
            'is_active' => 'Is Active',
            'quantity' => 'Quantity',
            'price' => 'Price',
            'currency' => 'Currency',
            'yacht_id' => 'Yacht ID',
            'equipment_id' => 'Equipment ID',
            'season_id' => 'Season ID',
            'comment' => 'Comment',
            'price_measure_id' => 'Price Measure ID',
            'calculation_type' => 'Calculation Type',
            'condition_string' => 'Condition String',
            'amount' => 'Amount',
            'amount_is_percentage' => 'Amount Is Percentage',
            'percentage_calculation_type' => 'Percentage Calculation Type',
            'minimum_price' => 'Minimum Price',
        ];
    }
    
    public static function sync( 
        $xml_id, 
        $comment,
        $quantity,
        $price,
        $currency,
        $yacht_id,
        $equipment_id,
        $season_id,
        $price_measure_id,
        $calculation_type,
        $condition_string,
        $amount,
        $amount_is_percentage,
        $percentage_calculation_type,
        $minimum_price,
        $xml_json_id = null
    ){ 
        $additionalEquipment = AdditionalEquipment::findOne([ 
                                            'xml_id' => $xml_id, 
                                            'yacht_id' => $yacht_id, 
                                            'equipment_id' => $equipment_id, 
                                            'season_id' => $season_id,
                                        ]); 
        if ($additionalEquipment){ 
            $additionalEquipment->is_active = 1; 
            $additionalEquipment->comment = $comment;
            $additionalEquipment->quantity = $quantity;
            $additionalEquipment->price = $price;
            $additionalEquipment->currency = $currency;
            $additionalEquipment->yacht_id = $yacht_id;
            $additionalEquipment->equipment_id = $equipment_id;
            $additionalEquipment->season_id = $season_id;
            $additionalEquipment->price_measure_id = $price_measure_id;
            $additionalEquipment->calculation_type = $calculation_type;
            $additionalEquipment->condition_string = $condition_string;
            $additionalEquipment->amount = $amount;
            $additionalEquipment->amount_is_percentage = $amount_is_percentage;
            $additionalEquipment->percentage_calculation_type = $percentage_calculation_type;
            $additionalEquipment->minimum_price = $minimum_price;
            $additionalEquipment->save(0); 
            return $additionalEquipment; 
        } 
        $additionalEquipment = new AdditionalEquipment(); 
        $additionalEquipment->comment = $comment;
        $additionalEquipment->quantity = $quantity;
        $additionalEquipment->price = $price;
        $additionalEquipment->currency = $currency;
        $additionalEquipment->yacht_id = $yacht_id;
        $additionalEquipment->equipment_id = $equipment_id;
        $additionalEquipment->season_id = $season_id;
        $additionalEquipment->price_measure_id = $price_measure_id;
        $additionalEquipment->calculation_type = $calculation_type;
        $additionalEquipment->condition_string = $condition_string;
        $additionalEquipment->amount = $amount;
        $additionalEquipment->amount_is_percentage = $amount_is_percentage;
        $additionalEquipment->percentage_calculation_type = $percentage_calculation_type;
        $additionalEquipment->minimum_price = $minimum_price;
        $additionalEquipment->xml_json_id = $xml_json_id;
        if ($additionalEquipment->save()){ 
            return $additionalEquipment; 
        } 
        return 0; 
    } 
    public static function inactiveAllSeason( 
        $wp_prefix, 
        $xml_id 
    ){ 
        $additionalEquipment = AdditionalEquipment::findOne([ 
                                            'xml_id' => $xml_id, 
                                            'is_active' => 1, 
                                        ]); 
        while ($additionalEquipment){ 
            $additionalEquipment->is_active = 0; 
            $additionalEquipment->save(0); 
            $additionalEquipment = AdditionalEquipment::findOne([ 
                'xml_id' => $xml_id, 
                'is_active' => 1, 
            ]); 
        } 
        return; 
    }
}
