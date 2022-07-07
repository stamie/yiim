<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "port".
 *
 * @property int $id
 * @property int|null $wp_id
 
 * @property int $xml_id
 * @property int $xml_json_id
 * @property int $is_active
 * @property int $location_id
 * @property int $company_id
 * @property string $check_in_time
 * @property string $check_out_time
 * @property float|null $xml_long
 * @property float|null $xml_lat
 * @property float|null $wp_long
 * @property float|null $wp_lat
 */
class Base extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'base';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['xml_id', 'xml_json_id', 'is_active', 'location_id', 'company_id'], 'integer'],
            [['xml_id', 'xml_json_id', 'location_id', 'company_id', 'check_in_time', 'check_out_time'], 'required'],
            [['check_in_time', 'check_out_time'], 'safe'],
            [['xml_long', 'xml_lat', 'wp_long', 'wp_lat'], 'number'],
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
            'xml_json_id' => 'Xml Json ID',
            'is_active' => 'Is Active',
            'location_id' => 'Location ID',
            'company_id' => 'Company ID',
            'check_in_time' => 'Check In Time',
            'check_out_time' => 'Check Out Time',
            'xml_long' => 'Xml Long',
            'xml_lat' => 'Xml Lat',
            'wp_long' => 'Wp Long',
            'wp_lat' => 'Wp Lat',
        ];
    }
}
