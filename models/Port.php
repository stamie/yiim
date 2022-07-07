<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "location".
 *
 * @property int $id
 * @property int|null $wp_id
 
 * @property int $xml_id
 * @property int $xml_json_id
 * @property string $name
 * @property int $is_active
 * @property int $region_id
 * @property float|null $xml_long
 * @property float|null $xml_lat
 * @property float|null $wp_long
 * @property float|null $wp_lat
 */
class Port extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'port';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'xml_id', 'xml_json_id', 'is_active', 'region_id', 'is_new'], 'integer'],
            [[ 'xml_id', 'xml_json_id', 'name', 'region_id'], 'required'],
            [['xml_long', 'xml_lat', 'wp_long', 'wp_lat'], 'number'],
            [['name'], 'string', 'max' => 100],
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
            'name' => 'Name',
            'is_active' => 'Is Active',
            'region_id' => 'Region ID',
            'xml_long' => 'Xml Long',
            'xml_lat' => 'Xml Lat',
            'wp_long' => 'Wp Long',
            'wp_lat' => 'Wp Lat',
        ];
    }
}
