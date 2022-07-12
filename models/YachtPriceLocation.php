<?php
namespace app\models;
use Yii;
/**
 * This is the model class for table "yacht_price_location".
 *
 * @property int $id
 * @property int|null $wp_id
 * @property int $xml_id
 * @property int $is_active
 * @property int $yachtPriceLocation_id
 * @property int $location_id
 */
class YachtPriceLocation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'yacht_price_location';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'xml_id', 'is_active', 'price_id', 'location_id'], 'integer'],
            [[ 'xml_id', 'price_id', 'location_id'], 'required'],

        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'xml_id' => 'Xml ID',
            'is_active' => 'Is Active',
            'price_id' => 'YachtPriceLocation ID',
            'location_id' => 'Location ID',
        ];
    }
    public static function sync(
        $xml_id,
        $price_id,
        $location_id       
    ){
        $yachtPriceLocation = YachtPriceLocation::findOne([
                                'xml_id' => $xml_id,
                                'price_id' => $price_id,
                                'location_id' => $location_id,
                            ]);
        if ($yachtPriceLocation){
            $yachtPriceLocation->is_active = 1;
            $yachtPriceLocation->save(0);
            return $yachtPriceLocation;
        }
        $yachtPriceLocation = new YachtPriceLocation();
        $yachtPriceLocation->xml_id = $xml_id;
        $yachtPriceLocation->price_id = $price_id;
        $yachtPriceLocation->location_id = $location_id;
        if ($yachtPriceLocation->save()){
            return $yachtPriceLocation;
        }
	return 0;
    }
    public static function inactiveAll(
        $xml_id, 
        $price_id
    ){
        $yachtPriceLocation = YachtPriceLocation::findOne([
                                            'price_id' => $price_id,
                                            'xml_id' => $xml_id,
                                            'is_active' => 1,
                                        ]);
        while ($yachtPriceLocation){
            $yachtPriceLocation->is_active = 0;
            $yachtPriceLocation->save(0);
            $yachtPriceLocation = YachtPriceLocation::findOne([
                'price_id' => $price_id,
                'xml_id' => $xml_id,
                'is_active' => 1,
            ]);
        }
        return;
    }
}

