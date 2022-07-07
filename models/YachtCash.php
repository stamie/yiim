<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "yacht_cash".
 *
 * @property int $yacht_id
 * @property string $date_from
 * @property string $json_value
 * @property int $xml_id
 * @property int $location_id
 * @property string $category
 * @property int $model_id
 * @property string $model
 * @property float|null $user_price
 * @property string|null $currency
 * @property int|null $beds
 * @property int|null $cabins
 * @property string|null $service_types
 * @property int|null $length
 * @property int|null $capacity
 * @property int|null $builder_year
 * @property string|null $update_datetime
 */
class YachtCash extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'yacht_cash';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['yacht_id', 'date_from', 'json_value', 'xml_id', 'location_id', 'category', 'model_id', 'model'], 'required'],
            [['yacht_id', 'xml_id', 'location_id', 'model_id', 'beds', 'cabins', 'length', 'capacity', 'builder_year'], 'integer'],
            [['date_from', 'update_datetime'], 'safe'],
            [['json_value'], 'string'],
            [['user_price'], 'number'],
            [['category', 'model', 'service_types'], 'string', 'max' => 250],
            [['currency'], 'string', 'max' => 5],
            [['yacht_id', 'date_from'], 'unique', 'targetAttribute' => ['yacht_id', 'date_from']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'yacht_id' => 'Yacht ID',
            'date_from' => 'Date From',
            'json_value' => 'Json Value',
            'xml_id' => 'Xml ID',
            'location_id' => 'Location ID',
            'category' => 'Category',
            'model_id' => 'Model ID',
            'model' => 'Model',
            'user_price' => 'User Price',
            'currency' => 'Currency',
            'beds' => 'Beds',
            'cabins' => 'Cabins',
            'service_types' => 'Service Types',
            'length' => 'Length',
            'capacity' => 'Capacity',
            'builder_year' => 'Builder Year',
            'update_datetime' => 'Update Datetime',
        ];
    }
}
