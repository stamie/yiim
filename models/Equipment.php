<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "equipment".
 *
 * @property int $id
 * @property int|null $wp_id
 
 * @property int $xml_id
 * @property int $xml_json_id
 * @property string $name
 * @property int $is_active
 * @property int $season_id
 * @property int|null $equipment_category_json_id
 */
class Equipment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'equipment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'xml_id', 'xml_json_id', 'is_active', 'equipment_category_json_id'], 'integer'],
            [[ 'xml_id', 'xml_json_id', 'name'], 'required'],
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

            'equipment_category_json_id' => 'Equipment Category Json ID',
        ];
    }
}
