<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ports_in_cities".
 *
 * @property int $id
 * @property int $xml_id
 * @property int $xml_json_port_id
 * @property int $wp_prefix_id
 * @property int|null $wp_port_id
 * @property int|null $cities_id
 */
class PortsInCities extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ports_in_cities';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['xml_id', 'xml_json_port_id', 'wp_prefix_id'], 'required'],
            [['xml_id', 'xml_json_port_id', 'wp_prefix_id', 'wp_port_id', 'cities_id'], 'integer'],
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
            'xml_json_port_id' => 'Xml Json Port ID',
            'wp_prefix_id' => 'Wp Prefix ID',
            'wp_port_id' => 'Wp Port ID',
            'cities_id' => 'Cities ID',
        ];
    }

    
}
