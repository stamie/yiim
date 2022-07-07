<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "price".
 *
 * @property int $id
 * @property int|null $wp_id
 
 * @property int $xml_id
 * @property int $xml_json_id
 * @property string $name
 * @property int $is_active
 * @property int $season_id
 * @property string $date_from
 * @property string $date_to
 * @property float $price
 * @property string $currency
 * @property string $type
 */
class Price extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'price';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['xml_id', 'is_active', 'season_id', 'yacht_id'], 'integer'],
            [['xml_id', 'season_id', 'date_from', 'date_to', 'price', 'currency', 'type', 'yacht_id'], 'required'],
            [['date_from', 'date_to'], 'safe'],
            [['price'], 'number'],

            [['currency'], 'string', 'max' => 10],
            [['type'], 'string', 'max' => 20],
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
            'season_id' => 'Season ID',
            'date_from' => 'Date From',
            'date_to' => 'Date To',
            'price' => 'Price',
            'currency' => 'Currency',
            'type' => 'Type',
        ];
    }
    public static function sync(

        $xml_id,

        $season_id,
        $date_from,
        $date_to,
        $price_,
        $currency,
        $type,
        $yacht_id

    ) {

        $price = Price::findOne([


            'xml_id' => $xml_id,
            'season_id' => $season_id,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'price' => $price_,
            'currency' => $currency,
            'type' => $type,
            'yacht_id' => $yacht_id
        ]);
        if ($price) {

            $price->is_active = 1;
            $price->save(0);
            // var_dump($price);exit ("vok");
            return $price;
        }

        $price = new Price();

        $price->xml_id = $xml_id;

        $price->season_id = $season_id;

        $price->date_from = $date_from;
        $price->date_to = $date_to;
        $price->price = $price_;
        $price->currency = $currency;
        $price->is_active = 1;
        $price->type = $type;
        $price->yacht_id = $yacht_id;

        if ($price->save()) {
            return $price;
        }

        return 0;
    }
    public static function inactiveAllYachtPrices($yacht_id)
    {
        $yacht = Yacht::findOne($yacht_id);
        if ($yacht) {
            Price::updateAll(["is_active" => 0], ["yacht_id" => $yacht->xml_json_id, "xml_id" => $yacht->xml_id]);
            return 1;
        }
        return 0;
    }
    public static function inactiveAll(
        $wp_prefix,
        $xml_id,
        $company_id

    ) {
        $yachts = Yacht::findAll(['company_id' => $company_id]);
        foreach ($yachts as $yacht) :
            $price = Price::findOne([

                'xml_id' => $xml_id,
                'is_active' => 1,
                'yacht_id' => $yacht->xml_json_id
            ]);
            while ($price) {
                YachtPriceLocation::inactiveAll($wp_prefix, $xml_id, $price->id);
                $price->is_active = 0;
                $price->save(0);

                $price = Price::findOne([
                    'xml_id' => $xml_id,
                    'is_active' => 1,
                    'yacht_id' => $yacht->xml_json_id
                ]);
            }
        endforeach;

        return;
    }
}
