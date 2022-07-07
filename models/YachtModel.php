<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "yacht_model".
 *
 * @property int $id
 * @property int|null $wp_id
 
 * @property int $xml_id
 * @property int $xml_json_id
 * @property string $name
 * @property int $is_active
 * @property int $category_xml_id
 * @property int $builder_xml_id
 * @property float $loa
 * @property float $beam
 * @property float $draft
 * @property int $cabins
 * @property int $wc
 * @property int $water_tank
 * @property float $fuel_tank
 * @property float $displacement
 */
class YachtModel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'yacht_model';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'xml_id', 'xml_json_id', 'is_active', 'category_xml_id', 'builder_xml_id', 'cabins', 'wc', 'water_tank', 'is_new'], 'integer'],
            [[ 'xml_id', 'xml_json_id', 'name', 'category_xml_id', 'builder_xml_id', 'loa', 'beam', 'draft', 'cabins', 'wc', 'water_tank', 'fuel_tank', 'displacement'], 'required'],
            [['loa', 'beam', 'draft', 'fuel_tank', 'displacement'], 'number'],
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
            'category_xml_id' => 'Category Xml ID',
            'builder_xml_id' => 'Builder Xml ID',
            'loa' => 'Loa',
            'beam' => 'Beam',
            'draft' => 'Draft',
            'cabins' => 'Cabins',
            'wc' => 'Wc',
            'water_tank' => 'Water Tank',
            'fuel_tank' => 'Fuel Tank',
            'displacement' => 'Displacement',
        ];
    }
}
